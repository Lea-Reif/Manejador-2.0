<?php

namespace App\Controllers;

use \CodeIgniter\Database\Exceptions\DatabaseException;
use App\Models\Manejador;

class Home extends BaseController
{

	public function __construct()
	{
		// parent::__construct();
		$this->manModel = new Manejador();
	}

	public function login()
	{
		
		if ($this->request->getMethod() !== 'post') {
			return View('login');
		}else{
			$pass =$this->request->getVar('password');
			if($pass === 'Orion1914')
			{
				session()->set(['loggedIn' =>true]);
			}
			return $this->response->redirect('/Home');
		}

	}

	public function logout()
	{
		session()->destroy();
		return $this->response->redirect('/Home');
	}
	public function index()
	{
		$databases = $this->manModel->getDb();
		foreach ($databases as $database) {
			if (isset($database->group))
				$db[$database->group][] = $database;
			else
				$db['Sin Grupo'][] = $database;
		}
		ksort($db);
		return View('index.php', ['dbs' => $db]);
	}

	function getTable($query)
	{
		$query = explode("from", $query);
		$query = explode(" ", $query[1]);
		$query = (strpos($query[1], ';') !== false) ? $query : explode(";", $query[1])[0];
		return $query;
	}

	function string_between_two_string($str, $starting_word, $ending_word)
	{
		$subtring_start = strpos($str, $starting_word);

		$subtring_start += strlen($starting_word);
		//Length of our required sub string 
		$size = strpos($str, $ending_word, $subtring_start) - $subtring_start;
		// Return the substring from the index substring_start of length size  
		return substr($str, $subtring_start, $size);
	}


	public function ejecutar()
	{
		if ($this->request->getMethod() !== 'post') {
			exit('WRONG METHOD');
		}
		// try {
		// if ($this->request->isAJAX()) {
		$data = service('request')->getPost('query');
		$json_file = $this->manModel->getDb();
		$data = $this->request->getPost() == [] ? $this->request->getJSON() : $this->request->getPost();
		// $data['id'] = explode(',',$data['id']);
		$data = json_decode(json_encode($data), true);
		sort($data['id'], SORT_NUMERIC);
		// exit(json_encode($data));
		$this->response->setHeader('Content-Type', 'application/json');
		foreach ($data['id'] as  $id) {
			$id = intval($id);
			$db = $json_file[$id];
			$config['hostname'] = $db->host;
			$config['username'] = $db->user;
			$config['password'] = $db->pass;
			$config['database'] = $db->name;
			$config['DBDriver'] = 'mysqli';
			$dbConn = db_connect($config);

			
			if (strstr($data['query'], "PROCEDURE") || explode(' ', trim(strtolower($data['query'])))[0] !== "select" ) {
				$nombre_proc = $this->string_between_two_string($data['query'], "CREATE  PROCEDURE", '(');
				$dbConn->query("DROP PROCEDURE IF EXISTS $nombre_proc"); 
				$dbConn->query($data['query']);
				if($dbConn->error()['code'] !== 0)
				{
					$errores[] = "Error en la db $db->name: " . $dbConn->error()['message'];
				}else{
					$correctas[] = $db->name . " correcto";
				}
			} else {
			

				$queries = explode(';', $data['query']);


				foreach ($queries as $key => $query) {
					if ($query == "") continue;
					try {
						$select = $dbConn->query($query);
					} catch (DatabaseException $e) {
						$errores[] = "Error en la db $db->name : Innacesible";
						continue;
					}
					if ($dbConn->error()['code']) {
						$errores[] = $dbConn->error()['message'];
						continue;
					}

					$table = '<div class="card strpied-tabled-with-hover"><h4 align="center" class="card-title">Datos desde ' . $db->name . '</h4>';

					$tbl = strstr((strtolower($data['query'])), "select") == true ? $this->getTable($query) : $data['query'];
					$select = $select->getResultArray();
					if (count($select) == 0) {
						$table .= '<div class="card-header "> <p class="card-category"> Tabla <b>' . $tbl . ' Vacia </b></p></div></div>';

						continue;
					}

					$columnas = [];
					foreach ($select[0] as $key => $value) {
						$columnas[] = $key;
					}
					$table .= '<div class="card-header "> <p class="card-category"> Tabla <b>' . $tbl . '</b></p></div><div class="card-body table-full-width table-responsive"><table class="table table-wrapper table-hover table-striped"><thead><tr>';

					foreach ($columnas as $value) {
						$value = ucwords($value);
						$table .= "<th>$value</th>";
					}

					$table .= '</tr></thead><tbody>';

					foreach ($select as $item) {
						$table .= '<tr>';
						foreach ($item as  $val) {
							$table .= "<td>$val</td>";
						}
						$table .= '</tr>';
					}

					$table .= '</tr></tbody></table></div>';
					$table .= '</div>
							</br>';
					$correctas[] = $table;
				}
			} 
		}
		$respuesta = [

			'correcto' => $correctas ?? null,
			'errores' => $errores ?? null
		];
		echo json_encode($respuesta);
	}

	public function consultas(){
        $consultas = $this->manModel->getConsultas();
        return View('consultas',['consultas'=>$consultas]);

	}
	
    public function getConsultas(){
        $consultas = $this->manModel->getConsultas();
        exit(json_encode($consultas));
    }
    public function saveConsulta(){
		$postData = json_encode($this->request->getPost());
        $this->manModel->saveConsulta(json_decode($postData,true));
    }

    public function updateConsulta(){

        $postData = json_encode($this->request->getPost());
        $json_file = json_decode($this->manModel->getConsultas(),true);
        $data = json_decode($postData,true);
        $consultas= $json_file;
        $array_final=[];
        foreach ($consultas as $consulta) {
            if($consulta['id'] == $data['id']){
                unset($data['id']);
                array_push($array_final,$data);
            }else{
                unset($consulta['id']);
                array_push($array_final,$consulta);
            }
            
        }


        $final_data = json_encode($array_final);
		file_put_contents(APPPATH.'../json/consultas.json', $final_data);
        return print_r($array_final);

    }

    public function updateGroups()
    {
        $this->manModel->updateGroups($this->request->getPost());
        exit();
    }
}
