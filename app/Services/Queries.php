<?php

namespace App\Services;

class Queries {

    public function createEmployeeDemographicsTable () {
        $query = "CREATE TABLE employee_demographics (employee_id int, first_name varchar(50), last_name varchar(50), age int, gender varchar(50));";
    }

    public function createEmployeeSalaryTable () {
        $query = "CREATE TABLE employee_salary (employee_id int, job_title varchar(50), salary int);";
    }

}
