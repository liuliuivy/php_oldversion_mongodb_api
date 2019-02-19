<?php
/**
 * Author: Camille BAUDRAS
 */

namespace App\Controllers;


interface RestControllerInterface
{
    public function create();

    public function index();

    public function get($id);

    public function update($id);

    public function delete($id);
}