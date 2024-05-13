WITH current_epoch AS (
  SELECT b.epoch_no
  FROM block b
  ORDER BY b.id DESC
  LIMIT 1
),
locked_supply AS (
  SELECT SUM (value)::TEXT AS "locked"
  FROM tx_out txo
  WHERE txo.address_has_script = true
    AND NOT EXISTS (
      SELECT txo2.id
      FROM tx_out txo2
        JOIN tx_in txi on txo2.tx_id = txi.tx_out_id
        AND txo2.index = txi.tx_out_index
      WHERE txo.id = txo2.id
    )
)
SELECT json_build_object('locked', locked_supply) AS "supply"
FROM locked_supply;