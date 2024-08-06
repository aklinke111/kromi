<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

$sql = "SET @sql = NULL;

        SELECT
          GROUP_CONCAT(DISTINCT
            CONCAT(
              'SUM(CASE WHEN year = ', year, ' THEN sales ELSE 0 END) AS `', year, '`'
            )
          ) INTO @sql
        FROM sales;

        SET @sql = CONCAT('SELECT product, ', @sql, ' FROM sales GROUP BY product');

        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt";
    $result = $db->query($sql);
    

