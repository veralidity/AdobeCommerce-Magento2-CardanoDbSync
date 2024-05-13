WITH current_epoch AS (
  SELECT b.epoch_no
  FROM block b
  ORDER BY b.id DESC
  LIMIT 1
),
reserves_supply AS (
  SELECT (
      SELECT reserves
      FROM ada_pots
      WHERE epoch_no = (
          SELECT *
          FROM current_epoch
        )
    )::TEXT AS "reserves_supply"
)
SELECT json_build_object('reserves', reserves_supply) AS "supply"
FROM reserves_supply;