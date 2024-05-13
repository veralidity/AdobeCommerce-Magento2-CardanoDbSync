WITH current_epoch AS (
  SELECT b.epoch_no
  FROM block b
  ORDER BY b.id DESC
  LIMIT 1
),
total_supply AS (
  SELECT (
      (
        SELECT 45000000000000000 - reserves
        FROM ada_pots
        ORDER BY epoch_no desc
        LIMIT 1
      )
    )::TEXT AS "total_supply"
)
SELECT json_build_object('total', total_supply) AS "supply"
FROM total_supply;