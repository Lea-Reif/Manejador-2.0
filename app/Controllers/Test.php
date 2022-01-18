<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Test extends BaseController
{
    public function index()
    {

        $databases = \App\Models\Manejador::getDb();
        $responses = [];
        foreach ($databases as $db) {
            //CREATE CONNECTION
            $config['hostname'] = $db->host;
            $config['username'] = $db->user;
            $config['password'] = $db->pass;
            $config['database'] = $db->name;
            $config['DBDriver'] = 'mysqli';
            $conection = db_connect($config);

            $tables = $conection->listTables();
            $response = [];
            foreach ($tables as $table) {
                $fields = $conection->getFieldData($table);
                $fields = array_map(fn($f) => (array)$f, $fields);
                $fields = array_column($fields, NULL, 'name');
                $response[$table] = $fields;
            }
            $responses[] = $response;
        }

        [$base, $comparison] = $responses;

        $result = $this->compareDatabases($base, $comparison);
        exit(json_encode($result));
    }

    private function array_equal($a, $b) {
        array_multisort($a);
        array_multisort($b);
        return ( serialize($a) === serialize($b) );
    }

    private function compareDatabases(array $base, array $comparison) : array
    {
        if($this->array_equal($base, $comparison)){
            return ['result' => 'no differences'];
        }
        $differences = [];
        foreach ($base as $table => $fields) {
            if(empty($comparison[$table])){
                $differences['not_exist'][$table] = $fields;
                continue;
            }

            if($this->array_equal($fields, $comparison[$table])){
                continue;
            }

            foreach ($fields as $field => $property) {
                $diff = array_diff_assoc($property, $comparison[$table][$field] ?? []);
                if(!empty($diff)){
                    $differences[$table][$field]['should_be'] = $diff ;
                }
            }

        }
        return $differences;
    }
}
