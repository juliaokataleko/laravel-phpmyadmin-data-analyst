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
        $employeesWithSalary = DB::select('
            SELECT employee_demographics.id,
            employee_demographics.first_name,
            employee_demographics.last_name,
            employee_salaries.job_title,employee_salaries.amount as salary FROM employee_demographics
            JOIN employee_salaries ON employee_demographics.id = employee_salaries.employee_id
            WHERE first_name <> "Michael"
            ORDER BY salary DESC
        ');

        // avarege salary
        $employeesWithSalary = DB::select('SELECT
            job_title,
            AVG(amount) as avg_salary
            FROM employee_demographics
            JOIN employee_salaries ON employee_demographics.id = employee_salaries.employee_id
            GROUP BY employee_salaries.job_title
        ');

        return response()->json([
            $employeesWithSalary
        ]);
    }
}
