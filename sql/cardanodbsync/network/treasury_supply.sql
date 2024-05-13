WITH current_epoch AS (
  SELECT b.epoch_no
  FROM block b
  ORDER BY b.id DESC
  LIMIT 1
),
treasury_supply AS (
  SELECT (
      SELECT treasury
      FROM ada_pots
      WHERE epoch_no = (
          SELECT *
          FROM current_epoch
        )
    )::TEXT AS "treasury_supply"
)
SELECT json_build_object('treasury', treasury_supply) AS "supply"
FROM treasury_supply;