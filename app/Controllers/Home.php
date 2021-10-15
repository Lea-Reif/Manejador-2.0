<?php

namespace App\Controllers;

use \CodeIgniter\Database\Exceptions\DatabaseException;
use App\Models\Manejador;

class Home extends BaseController
{

	public function __construct()
	{
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, x-api-key, Content-Type, Accept, Authorization");
		header('Access-Control-Allow-Methods: GET, POST');
		$this->manModel = new Manejador();
	}

	public function login()
	{

		if ($this->request->getMethod() !== 'post') {
			return View('login');
		} else {
			$passArray = json_decode(file_get_contents(APPPATH . '../json/access.json'), true);
			$passArray = array_map(function ($p) {
				return $p['pass'];
			}, $passArray);

			$pass = $this->request->getVar('password');
			if (in_array($pass, $passArray)) {
				session()->set(['loggedIn' => true]);
			}
			return $this->response->redirect(base_url('/Home'));
		}
	}

	public function logout()
	{
		session()->destroy();
		return $this->response->redirect(base_url('/Home'));
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
		$splited_query = explode("from", $query);
		if( count($splited_query) == 1 )  $splited_query = explode("FROM", $query) ;
		
		if( count($splited_query) == 1 ) return $query;
		$query = explode(" ", $splited_query[1]);
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
		$data = service('request')->getPost('query');
		$json_file = $this->manModel->getDb();
		$data = $this->request->getPost() == [] ? $this->request->getJSON() : $this->request->getPost();
		$data = json_decode(json_encode($data), true);
		sort($data['id'], SORT_NUMERIC);
		$this->response->setHeader('Content-Type', 'application/json');

		if (strstr($data['query'], "CREATE")) {
			$query = $data['query'];
			$type = strtoupper(explode(' ', trim(substr($query, strpos($query, 'CREATE') + strlen('CREATE'), 10)))[0]);

			switch ($type) {
				case 'TRIGGER':
					preg_match("/(AFTER|BEFORE)/",$query,$math);
					$nombre_proc = $this->string_between_two_string($data['query'], "CREATE {$type}", $math[0]);
					break;
				case 'PROCEDURE':
					$nombre_proc = $this->string_between_two_string($data['query'], "CREATE {$type}", '(');
					break;
				case 'FUNCTION':
					$nombre_proc = $this->string_between_two_string($data['query'], "CREATE {$type}", '(');
					break;
				default:
					goto next;
					break;
			}
			$dropStmt = "DROP {$type} {$nombre_proc}";
			foreach ($data['id'] as  $id) {
				$id = intval($id);
				$db = $json_file[$id];
				$config['hostname'] = $db->host;
				$config['username'] = $db->user;
				$config['password'] = $db->pass;
				$config['database'] = $db->name;
				$config['DBDriver'] = 'mysqli';
				$dbConn = db_connect($config);

				$dbConn->query($dropStmt);
				$dbConn->query($data['query']);
				if ($dbConn->error()['code'] !== 0) {
					$errores[] = "Error en la db $db->db: " . $dbConn->error()['message'];
				} else {
					$correctas[] = $db->db . " correcto";
				}
			}
		} else {
			next:
			$queries = explode(';', $data['query']);

			foreach ($queries as  $query) {
				$query = ltrim($query);
				if ($query == "") continue;
				foreach ($data['id'] as  $id) {
					$id = intval($id);
					$db = $json_file[$id];
					$config['hostname'] = $db->host;
					$config['username'] = $db->user;
					$config['password'] = $db->pass;
					$config['database'] = $db->name;
					$config['DBDriver'] = 'mysqli';
					$dbConn = db_connect($config);

					try {
						$select = $dbConn->query($query);
					} catch (DatabaseException $e) {
						$errores[] = "Error en la db $db->db : Innacesible";
						continue;
					}
					if ($dbConn->error()['code']) {
						$errores[] = $dbConn->error()['message'];
						continue;
					}
					if ($select->connID->field_count === 0 &&  count($select->getResultArray()) === 0) {
						$correctas[] = $db->db . ". correcto";
						continue;
					} else {
						$table = '<div class="card strpied-tabled-with-hover"><h4 align="center" class="card-title">Datos desde ' . $db->db . '</h4>';

						$tbl = strstr((strtolower($query)), "select") == true ? $this->getTable($query) : $query;
						$select = $select->getResultArray();
						if (count($select) == 0) {
							$table .= '<div class="card-header "> <p class="card-category"> Tabla <b>' . $tbl . ' Vacia </b></p></div></div>';
							$correctas[] = $table;

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
		}
		$respuesta = [

			'correcto' => $correctas ?? null,
			'errores' => $errores ?? null
		];
		echo json_encode($respuesta);
	}


	public function consultas()
	{
		$consultas = $this->manModel->getConsultas();
		return View('consultas', ['consultas' => $consultas]);
	}

	public function getConsultas()
	{
		$consultas = $this->manModel->getConsultas();
		exit(json_encode($consultas));
	}
	public function saveConsulta()
	{
		$postData = json_encode($this->request->getPost());
		$this->manModel->saveConsulta(json_decode($postData, true));
	}

	public function updateConsulta()
	{

		$datos = $this->request->getPost();
		$id = $datos['id'];
		$consultas = $this->manModel->getConsultas();
		$array_final = [];
		foreach ($consultas as  $consulta) {
			if ($consulta['id'] == $id) {
				unset($datos['id']);
				array_push($array_final, $datos);
			} else {
				unset($consulta['id']);
				array_push($array_final, $consulta);
			}
		}


		$final_data = json_encode($array_final);
		file_put_contents(APPPATH . '../json/consultas.json', $final_data);
		$consultas = $this->manModel->getConsultas();

		exit('0');
	}

	public function deleteConsulta()
	{
		$datos = $this->request->getPost();
		$id = $datos['id'];
		$consultas = $this->manModel->getConsultas(false);
		unset($consultas[$id]);
		$array_final = [];
		$consultas = array_map(function ($c) use (&$array_final) {
			unset($c['id']);
			$array_final[] = $c;
			return $c;
		}, $consultas);
		file_put_contents(APPPATH . '../json/consultas.json', json_encode($array_final));
		exit('0');
	}

	public function updateGroups()
	{
		$this->manModel->updateGroups($this->request->getPost());
		exit();
	}
	
	public function addDb()
	{
		$llaves = ["name", "user", "pass", "host", "port", "group", "db"];
		$datos = $this->request->getPost();

		if( empty($datos) || !empty( array_diff( $llaves, array_keys($datos) ) ) ) exit('{"consulta":"error al completar la consulta"}');
		exit($this->manModel->addDb($this->request->getPost()));
	}
}
