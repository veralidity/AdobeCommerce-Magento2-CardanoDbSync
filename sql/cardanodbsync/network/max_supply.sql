WITH current_epoch AS (
  SELECT b.epoch_no
  FROM block b
  ORDER BY b.id DESC
  LIMIT 1
),
max_supply AS (
  SELECT (SELECT 45000000000000000)::TEXT AS "max_supply"
)
SELECT json_build_object('max', max_supply) AS "supply"
FROM max_supply;