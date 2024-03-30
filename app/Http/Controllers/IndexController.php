<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index() {
        // return view('welcome');

        // select all employees
        $employees = DB::select("SELECT * FROM employee_demographics");

        // limit rows
        $employees = DB::select("SELECT * FROM employee_demographics limit 5");

        // distinct
        $employees = DB::select("SELECT DISTINCT(id) FROM employee_demographics limit 5");
        $employees = DB::select("SELECT DISTINCT(gender) FROM employee_demographics");

        // count
        $employees = DB::select("SELECT COUNT(last_name) AS lastNameCount from employee_demographics ");

        // max
        // $employees = DB::select("select DISTINCT(id) from employee_demographics limit 5");
        $salaries = DB::select('SELECT MAX(amount) FROM employee_salaries');

        // min
        $salaries = DB::select('SELECT MIN(amount) FROM employee_salaries');

        // average
        $salaries = DB::select('SELECT AVG(amount) FROM employee_salaries');

        // where
        $employees = DB::select("SELECT * FROM employee_demographics WHERE first_name = 'Jim'");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE first_name <> 'Jim'");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE age > 50");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE age < 30");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE age <= 30 AND gender = 'M'");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE age <= 30 OR gender = 'M'");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE last_name LIKE 'S%'"); // start with S
        $employees = DB::select("SELECT * FROM employee_demographics WHERE last_name LIKE 'S%o%'"); // start with S and contains o
        $employees = DB::select("SELECT * FROM employee_demographics WHERE first_name IS NULL");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE first_name IS NOT NULL");
        $employees = DB::select("SELECT * FROM employee_demographics WHERE first_name = 'Hal'");
        // in
        $employees = DB::select("SELECT * FROM employee_demographics WHERE first_name IN ('Hal', 'Dallin', 'Tommie')");

        // Group By, Order By Statement
        $employees = DB::select("SELECT gender, COUNT(gender) FROM employee_demographics GROUP BY gender");
        $employees = DB::select("SELECT gender, age, COUNT(gender) FROM employee_demographics WHERE age > 80 GROUP BY gender, age");
        $employees = DB::select("SELECT gender, age, COUNT(gender) as count_gender FROM employee_demographics WHERE age > 88 GROUP BY gender, age
            ORDER BY gender");
        $employees = DB::select("SELECT * FROM employee_demographics ORDER BY age, gender DESC");
        // order by column number
        $employees = DB::select("SELECT * FROM employee_demographics ORDER BY 4");
        return response()->json($employees);
    }

    public function join() {
        // Inner Joins,/Left/Right Outer Joins
        $employees = DB::select("SELECT * FROM employee_demographics");
        $salaries = DB::select("SELECT * FROM employee_salaries");

        // inner join
        $employeesWithSalary = DB::select('
            SELECT * FROM employee_demographics
            INNER JOIN employee_salaries ON employee_demographics.id = employee_salaries.employee_id
        ');

        // left join
        $employeesWithSalary = DB::select('
            SELECT * FROM employee_demographics
            LEFT JOIN employee_salaries ON employee_demographics.id = employee_salaries.employee_id
        ');

        // right join
        $employeesWithSalary = DB::select('
            SELECT employee_demographics.first_name,employee_demographics.last_name,employee_salaries.job_title FROM employee_demographics
            RIGHT JOIN employee_salaries ON employee_demographics.id = employee_salaries.employee_id
        ');

        // USE CASE

        // top salaries
        $topSalaries = DB::select('
            SELECT employee_demographics.id,
            employee_demographics.first_name,
            employee_demographics.last_name,
            employee_salaries.job_title,employee_salaries.amount as salary FROM employee_demographics
            JOIN employee_salaries ON employee_demographics.id = employee_salaries.employee_id
            WHERE first_name <> "Michael"
            ORDER BY salary DESC
        ');

        // avarege salary
        $averageSaleries = DB::select('SELECT
            job_title,
            AVG(amount) as avg_salary
            FROM employee_demographics
            JOIN employee_salaries ON employee_demographics.id = employee_salaries.employee_id
            GROUP BY employee_salaries.job_title
        ');

        return response()->json([
            $averageSaleries
        ]);
    }

    public function union() {
        // Union, Union All
        $employees = DB::select("SELECT * FROM employee_demographics");
        return response()->json($employees);
    }

    public function case() {

        // Case Statement
        $employees = DB::select("
            SELECT first_name, last_name,age,
            CASE
                WHEN age > 30 THEN 'Old'
                WHEN age BETWEEN 27 AND 30 THEN 'Young'
                ELSE 'Baby'
            END AS age_level
            FROM employee_demographics
            WHERE age IS NOT NULL
        ");

        // change salaries to different jobs
        $employees = DB::select("
            SELECT ed.id as employee_id, first_name, last_name, amount as salary,job_title,
            CASE
                WHEN job_title = 'Developer' THEN amount+(amount*0.10)
                WHEN job_title = 'Comercial' THEN amount+(amount*0.07)
                WHEN job_title = 'Marketer' THEN amount+(amount*0.05)
                ELSE amount+(amount*0.03)
            END as salary_increased
            FROM employee_demographics AS ed
            JOIN employee_salaries AS es ON es.employee_id = ed.id
        ");

        return response()->json($employees);
    }

    public function having() {
        // Having

        // jobs with more employees
        // we use HAVING instead of WHERE when is not real field in the database
        $employees = DB::select("
            SELECT job_title, COUNT(job_title) FROM employee_demographics as ed
            JOIN employee_salaries AS es ON es.employee_id = ed.id
            GROUP BY job_title
            HAVING COUNT(job_title) > 1
        ");

        // average salary
        $employees = DB::select("
            SELECT job_title, AVG(amount) FROM employee_demographics as ed
            JOIN employee_salaries AS es ON es.employee_id = ed.id
            GROUP BY job_title
            HAVING AVG(amount) > 2000
            ORDER BY AVG(amount) DESC
        ");

        return response()->json($employees);
    }

    public function crud() {

        // update
        $update = DB::update("
            UPDATE employee_demographics SET last_name = 'Kataleko II'
            WHERE id = 1
        ");

        // delete
        $delete = DB::delete("DELETE FROM employee_demographics WHERE id = 2");

        // select
        $employees = DB::select("
            SELECT ed.id as employee_id, first_name, last_name, amount as salary,job_title,
            CASE
                WHEN job_title = 'Developer' THEN amount+(amount*0.10)
                WHEN job_title = 'Comercial' THEN amount+(amount*0.07)
                WHEN job_title = 'Marketer' THEN amount+(amount*0.05)
                ELSE amount+(amount*0.03)
            END as salary_increased
            FROM employee_demographics AS ed
            JOIN employee_salaries AS es ON es.employee_id = ed.id
        ");

        return response()->json($employees);

    }

    public function aliasing() {
        // aliasing
        $employees = DB::select("
            SELECT demo.id as employee_id, first_name as fname,
            CONCAT(first_name, ' ', last_name) as fullname, amount as salary
            FROM employee_demographics as demo
            JOIN employee_salaries AS sal on demo.id = sal.employee_id
        ");

        return response()->json($employees);
    }

    public function partition() {

        // Partition By
        $employees = DB::select("
            SELECT first_name, last_name, gender, amount AS salary,
            COUNT(gender) OVER (PARTITION BY gender) as total_gender
            FROM employee_demographics dem
            JOIN employee_salaries sal ON sal.employee_id = dem.id
        ");

        $employees = DB::select("
            SELECT first_name, last_name, gender, amount AS salary,
            COUNT(gender) as total_gender
            FROM employee_demographics dem
            JOIN employee_salaries sal ON sal.employee_id = dem.id
            GROUP BY first_name, last_name, gender, amount
        ");

        $employees = DB::select("
            SELECT gender,
            COUNT(gender) as total_gender
            FROM employee_demographics dem
            JOIN employee_salaries sal ON sal.employee_id = dem.id
            GROUP BY gender
        ");

        return response()->json($employees);
    }

    public function ctes() {
        // ctes
        // subqueries
        $employees = DB::select("
            WITH CTE_employees AS (
                SELECT first_name, last_name, gender, amount as salary,
                COUNT(gender) OVER (PARTITION BY gender) AS total_gender,
                AVG(amount) OVER (PARTITION BY gender) AS avg_salary
                FROM employee_demographics emp
                JOIN employee_salaries sal ON emp.id = sal.employee_id
                WHERE amount > 1500
            )
            SELECT first_name,avg_salary FROM CTE_employees
        ");

        return response()->json($employees);
    }

    public function tempTables() {

        // drop tables
        DB::statement("DROP TABLE IF EXISTS temp_employees");
        DB::statement("DROP TABLE IF EXISTS temp_employees2");

        // temporary tables
        $createTempTable = DB::statement("
            CREATE TEMPORARY TABLE temp_employees (
                id int,
                job VARCHAR(100),
                salary int
            )
        ");

        // insert
        // $insert = DB::insert("
        // INSERT INTO temp_employees
        // VALUES(1,'Developer', 5000),

        // ");

        // insert from another table
        $insert = DB::insert("
            INSERT INTO temp_employees (id,job,salary)
            SELECT emp.id,sal.job_title,sal.amount FROM employee_demographics as emp
            JOIN employee_salaries as sal ON sal.employee_id = emp.id
        ");

        // create another temporary table
        $createTempTable2 = DB::statement("
            CREATE TEMPORARY TABLE temp_employees2 (
                job_title VARCHAR(100),
                employees_per_job int,
                avg_age int,
                avg_salary int
            )
        ");

        // insert into second table
        $insert = DB::insert("
            INSERT INTO temp_employees2 (job_title,employees_per_job,avg_age,avg_salary)
            SELECT sal.job_title,COUNT(job_title),AVG(age),AVG(sal.amount) FROM employee_demographics as emp
            JOIN employee_salaries as sal ON sal.employee_id = emp.id
            GROUP BY job_title
        ");

        $employees = DB::select("
            SELECT * FROM temp_employees2
        ");

        return response()->json($employees);
    }

    public function functions() {

        // String functions - TRIM, LTRIM, RTRIM, Replace, Subsctring,Uppercase,Lower
        DB::statement("CREATE TABLE IF NOT EXISTS employee_errors (
            employee_id int,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            error VARCHAR(100) NULL
        )");

        // DB::insert("
        //     INSERT INTO employee_errors VALUES(1,'Julian   ', '   Kata',''),
        //     (3,'Mary edited  ', 'Maria  ',''),
        //     (1,'Peter         yea', 'Enderson  ','')
        // ");

        // select
        $employees = DB::select("
            SELECT employee_id,
            first_name,
            TRIM(first_name) as first_name_trimed,
            last_name,
            TRIM(last_name) as last_name_trimed
            FROM employee_errors
        ");

        // using replace
        $employees = DB::select("
            SELECT employee_id,
            first_name,
            TRIM(REPLACE(first_name, '  ', '')) as first_name_trimed_replaced,
            last_name,
            TRIM(REPLACE(last_name, '  ', '')) as last_name_trimed
            FROM employee_errors
        ");

        // substring
        $employees = DB::select("
            SELECT dem.first_name,
            err.first_name
            FROM employee_errors AS err
            JOIN employee_demographics AS dem ON err.employee_id = dem.id
        ");

        // upper and lower
        $employees = DB::select("
        SELECT
        first_name,
        LOWER(first_name),
        UPPER(last_name)
        FROM employee_errors
        ");

        return response()->json($employees);

    }

    public function procedures() {
        // Stroed procedures
        // create a sql routine to be called by a commom name

        // $employees = DB::select("
        //     SELECT
        //     first_name,
        //     LOWER(first_name),
        //     UPPER(last_name)
        //     FROM employee_errors
        // ");

        // DB::statement("
        //     CREATE PROCEDURE all_employees()
        //     BEGIN
        //         SELECT * FROM employee_demographics;
        //     END
        // ");

        $employees = DB::select("CALL all_employees");

        // create temp tble with procedure
        DB::statement("
            CREATE PROCEDURE temp_employees()
            BEGIN
                CREATE TEMPORARY TABLE temp_employee_table (name VARCHAR(50),age int);
            END
        ");

        DB::statement("CALL temp_employees");

        DB::statement("
            INSERT INTO temp_employee_table
            SELECT first_name,age FROM employee_demographics
        ");

        $employees = DB::select("SELECT * FROM temp_employee_table");

        return response()->json($employees);
    }

    public function subqueries() {
        // $employee_salaries = DB::select("
        //     SELECT * FROM employee_salaries
        // ");

        $employee_salaries = DB::select("
            SELECT employee_id, amount,
            (SELECT AVG(amount) FROM employee_salaries) AS average_salary
            FROM employee_salaries
        ");

        // subqueries with partition
        $employee_salaries2 = DB::select("
            SELECT employee_id, amount,
            AVG(amount) OVER() AS average_salary
            FROM employee_salaries
        ");

        // using group by
        $employee_salaries3 = DB::select("
            SELECT employee_id, amount,
            AVG(amount) AS average_salary
            FROM employee_salaries
            GROUP BY employee_id, amount
            ORDER BY 1,2
        ");

        // using subquery in FROM, its like creating a temporary table
        $employee_salaries3 = DB::select("
            SELECT * FROM (SELECT employee_id, amount,
            AVG(amount) OVER() AS average_salary
            FROM employee_salaries) as employee_salary
        ");

        // subquery in where
        $employee_salaries3 = DB::select("
            SELECT s.employee_id, CONCAT(e.first_name, ' ', e.last_name)as employee_name, s.amount, s.job_title
            FROM employee_salaries as s
            JOIN employee_demographics as e ON e.id = s.employee_id
            WHERE employee_id IN (SELECT id FROM employee_demographics WHERE age > 40)
        ");

        return response()->json($employee_salaries3);
    }

}
