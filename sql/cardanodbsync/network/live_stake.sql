WITH current_epoch AS (
  SELECT b.epoch_no
  FROM block b
  ORDER BY b.id DESC
  LIMIT 1
),
active_stake AS (
  SELECT (
      (
        SELECT COALESCE(SUM(txo.value), 0)
        FROM tx_out txo
          LEFT JOIN tx_in txi ON (txo.tx_id = txi.tx_out_id)
          AND (txo.index = txi.tx_out_index)
        WHERE txi IS NULL
      ) + (
        SELECT COALESCE(SUM(amount), 0)
        FROM reward
        WHERE spendable_epoch <= (
            SELECT *
            FROM current_epoch
          )
      ) - (
        SELECT COALESCE(SUM(amount), 0)
        FROM withdrawal
      )
    )::TEXT AS "live_stake"
)
SELECT json_build_object('live', live_stake) AS "stake"
FROM active_stake;