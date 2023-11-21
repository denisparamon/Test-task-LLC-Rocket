Нам понадобится следующая структура базы данных с тремя таблицами: рабы (slaves), категории (categories) и пол (gender). Вот SQL-запросы для создания таблиц и выборок:

Создание таблиц:

sql
Copy code
CREATE TABLE categories (
    category_id INT PRIMARY KEY,
    category_name VARCHAR(255)
);

CREATE TABLE slaves (
    slave_id INT PRIMARY KEY,
    name VARCHAR(255),
    weight DECIMAL(5, 2),
    price DECIMAL(10, 2),
    category_id INT,
    gender_id INT,
    FOREIGN KEY (category_id) REFERENCES categories (category_id),
    FOREIGN KEY (gender_id) REFERENCES gender (gender_id)
);

CREATE TABLE gender (
    gender_id INT PRIMARY KEY,
    gender_name VARCHAR(10)
);

Запросы:

1. Минимальная, максимальная и средняя стоимость рабов весом более 60 кг:

sql
Copy code
SELECT
    MIN(price) AS min_price,
    MAX(price) AS max_price,
    AVG(price) AS avg_price
FROM slaves
WHERE weight > 60;

2. Категории с более чем 10 рабами:

sql
Copy code
SELECT
    category_name
FROM categories
WHERE (
    SELECT COUNT(*)
    FROM slaves
    WHERE slaves.category_id = categories.category_id
) > 10;

3. Категория с наибольшей суммарной стоимостью рабов:

sql
Copy code
SELECT
    category_name
FROM categories
WHERE category_id = (
    SELECT category_id
    FROM slaves
    GROUP BY category_id
    ORDER BY SUM(price) DESC
    LIMIT 1
);

4. Категории, где количество мужчин больше, чем женщин:

sql
Copy code
SELECT
    category_name
FROM categories
WHERE (
    SELECT COUNT(*)
    FROM slaves
    WHERE slaves.category_id = categories.category_id
        AND slaves.gender_id = (SELECT gender_id FROM gender WHERE gender_name = 'Мужчина')
) > (
    SELECT COUNT(*)
    FROM slaves
    WHERE slaves.category_id = categories.category_id
        AND slaves.gender_id = (SELECT gender_id FROM gender WHERE gender_name = 'Женщина')
);

5. Количество рабов в категории "Для кухни" (включая все вложенные категории):

sql
Copy code
WITH RECURSIVE subcategories AS (
    SELECT category_id
    FROM categories
    WHERE category_name = 'Для кухни'
    UNION
    SELECT c.category_id
    FROM categories c
    JOIN subcategories sc ON c.parent_category_id = sc.category_id
)
SELECT
    COUNT(*)
FROM slaves
WHERE category_id IN (SELECT category_id FROM subcategories);