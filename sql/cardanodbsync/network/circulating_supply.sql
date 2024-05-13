WITH current_epoch AS (
  SELECT b.epoch_no
  FROM block b
  ORDER BY b.id DESC
  LIMIT 1
),
circulating_supply AS (
  SELECT (
      (
        SELECT COALESCE(SUM(txo.value), 0)
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
    )::TEXT AS "circulating_supply"
  FROM tx_out txo
    LEFT JOIN tx_in txi ON (txo.tx_id = txi.tx_out_id)
    AND (txo.index = txi.tx_out_index)
  WHERE txi IS NULL
)
SELECT json_build_object('circulating', circulating_supply) AS "supply"
FROM circulating_supply;