<?php  
class ComandaController extends \HXPHP\System\Controller
{
	public function __construct($configs){
		parent::__construct($configs);
		
	}

	public function indexAction(){
		$this->view->setfile('index');
	}

	public function menuAction(){
		$this->view->setfile('index');
	}

	public function ativaAction(){
		$this->view->setfile('listaativa');
		$pedidos = Request::all(array('conditions' => 'status = "Aberta"', 'order' => 'id desc'));
		$this->view->setvar('pedidos', $pedidos);
	}

	public function redirecionaAction(){	
		$this->view->setfile('redireciona');
		setcookie('pedido', null, -1, '/');
	}

	public function limpaAction(){	
		$this->view->setfile('limpa');
		setcookie('pedido', null, -1, '/');
	}


	public function cadastraAction($p){
		$pedido = Request::find($_COOKIE['pedido']); 
		$teste = RequestItem::find_all_by_requests_id($pedido->id);
		$pedido->status = 'Aberta';
		$pedido->viagem = ($p == 1 ? 1 : 0);
		$pedido->mesa = ($p == 1 ? NULL: ((int)$p - 2));
		$pedido->save();
		$mensagem = array('success', 'Sucesso', 'Comanda cadastrada com sucesso.');		
		$this->view->setfile('listaativa');
		$pedidos = Request::all(array('conditions' => 'status = "Aberta"', 'order' => 'id desc'));
		$this->view->setvar('pedidos', $pedidos);
		setcookie('pedido', null, -1, '/'); 
		$this->load('Helpers\Alert', $mensagem);
	}

	public function alteraAction($p, $mesa){
		$pedido = Request::find($_COOKIE['pedido']); 
		$teste = RequestItem::find_all_by_requests_id($pedido->id);
		if(!empty($teste)){
			$username = "root";
			$password = "";
			$conn = new PDO("mysql:host=localhost;dbname=major", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare('UPDATE requests SET viagem = :viagem, mesa = :mesa WHERE id = :id');
			$stmt->execute(array(
				':id'   => $pedido->id,
				':mesa' => ($p == 1 ? NULL: $mesa),
				':viagem' => ($p == 1 ? 1 : 0)
			));
			$mensagem = array('success', 'Sucesso', 'Comanda alterada com sucesso.');	
		}
		else{
			$pedido->status = "Cancelada";
			$pedido->save();
			$mensagem = array('success', 'Sucesso', 'Comanda cancelada com sucesso.');	
		}

		$teste = $_COOKIE;
		unset($teste['sec_session_id']);
		unset($teste['pedido']);
		unset($teste['viagem']);
		unset($teste['mesa']);		
		unset($teste['observacao']);
		$prod = array_keys($teste);
		if (!empty($_COOKIE)){
			foreach ($_COOKIE as $key => $value){
				setcookie($key, $value, time() - 3600, '/');
			}
		} 

		$pedidos = Request::all(array('conditions' => 'status = "Aberta"', 'order' => 'id desc'));
		$this->view->setfile('listaativa');
		$this->view->setvar('pedidos', $pedidos);
		$this->load('Helpers\Alert', $mensagem);
		$pedido = Request::find($_COOKIE['pedido']);
	}

	public function observacaoAction($p){
		$this->view->setfile('observacaocomanda');
		$this->view->setvar('pedido', $p);
		if(isset($_COOKIE['pedido'])){
			$pedido = Request::find($_COOKIE['pedido']);
			if(!empty($pedido->observacao)){
				$this->view->setvar('obs', $pedido->observacao);
			}
		}
		$post = $this->request->post();
		if(!empty($post)){
			if(!isset($_COOKIE['pedido'])){
				$pedido = Request::create(array('observacao'=> $post['observacao'], 'status' => 'Anotando', 'viagem' => ($p == 1 ? 1 : 0), 'mesa'=> ($p == 1 ? NULL : $p - 2)));
				setcookie('pedido',$pedido->id, time()+36000, '/');
				$mensagem = array('success', 'Sucesso', 'Observação adicionada com sucesso.');
			}
			else{
				$pedido = Request::find($_COOKIE['pedido']);
				$pedido->observacao = $post['observacao'];
				$pedido->save();
				$mensagem = array('success', 'Sucesso', 'Observação adicionada com sucesso.');
			}
			$this->view->setvar('viagem', ($p == 1 ? 1 : 2));
			if($p != 1){
				$this->view->setvar('mesa', $p - 2);
			}//if transforma mesa e viagem 
			$itens = RequestItem::find_all_by_requests_id((isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id));
			$this->view->setfile('abrircomanda');
			$this->view->setvar('itens', $itens);
			$this->load('Helpers\Alert', $mensagem);
		}
	}

	public function observacaoAlteracaoAction($p){
		$this->view->setfile('observacaoalteracao');
		$this->view->setvar('pedido', $p);
		$this->view->setvar('id',$_COOKIE['pedido']);
		if(isset($_COOKIE['pedido'])){
			$pedido = Request::find($_COOKIE['pedido']);
			if(!empty($pedido->observacao)){
				$this->view->setvar('obs', $pedido->observacao);
			}
		}
		$post = $this->request->post();
		if(!empty($post)){			
			$pedido = Request::find($_COOKIE['pedido']);
			$pedido->observacao = $post['observacao'];
			$pedido->save();
			$mensagem = array('success', 'Sucesso', 'Observação alterada com sucesso.');
			$this->view->setvar('viagem', ($p == 1 ? 1 : 2));
			if($p != 1){
				$this->view->setvar('mesa', $p - 2);
			}
			//if transforma mesa e viagem 
			$itens = RequestItem::find_all_by_requests_id((isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id));
			$this->view->setfile('alterarcomanda');
			$this->view->setvar('itens', $itens);
			$this->load('Helpers\Alert', $mensagem);
		}
	}

	public function obsAlteracaoAction($p){
		$this->view->setfile('observacaocomanda');
		$this->view->setvar('pedido', $p);
		if(isset($_COOKIE['pedido'])){
			$pedido = Request::find($_COOKIE['pedido']);
			if(!empty($pedido->observacao)){
				$this->view->setvar('obs', $pedido->observacao);
			}
		}
	}


	public function abrirAction($viagem, $mesa = null){
		$this->view->setfile('abrircomanda');
		$this->view->setvar('viagem', $viagem);
		if(!empty($mesa)){
			$this->view->setvar('mesa', $mesa);
		}
		if(isset($_COOKIE['pedido'])){
			$itens = RequestItem::find_all_by_requests_id($_COOKIE['pedido']);
			$this->view->setvar('itens', $itens);
		}
	}



	public function alterarAction($id, $viagem = null, $mesa = null){
		
		$this->view->setfile('alterarcomanda');
		$this->view->setvar('id', $id);
		if(!empty($viagem)){
			$this->view->setvar('viagem', $viagem);
			if(!empty($mesa)){
				$this->view->setvar('mesa', $mesa);
			}//mesa não é nulo
		}//fim if viagem não nulo
		else{
			$pedido = Request::find($id);
			setcookie('pedido', $pedido->id, time()+36000, '/');
			if(empty($pedidos->errors)){
				$this->view->setvar('viagem', ($pedido->viagem == 0 ? 2 : 1));
				if(!isset($_COOKIE['viagem']) and !isset($_COOKIE['mesa'])){
					setcookie('viagem', $pedido->viagem, time()+36000, '/');
					setcookie('mesa', $pedido->mesa, time()+36000, '/');
					setcookie('observacao', $pedido->observacao, time()+36000, '/');
				}
				if(!empty($pedido->mesa)){
					$this->view->setvar('mesa', $pedido->mesa);
				}//mesa n é nula
			}//fim if pedido com id valido
			else{
				$mensagem = array('danger', 'Erro', 'Id inválido.');
				$this->load('Helpers\Alert', $mensagem);
			}//fim else pedido com id invalido
		}//fim else viagem vazio
		if(isset($_COOKIE['pedido']) or isset($pedido->id)){
			$itens = RequestItem::find_all_by_requests_id((isset($_COOKIE['pedido']) ? $_COOKIE['pedido'] : $pedido->id));
			foreach ($itens as $i){
				if(!isset($_COOKIE[$i->products_id])){
					setcookie($i->products_id, $i->qtd, time()+36000, '/');
				}//cookie produto n foi cadastrado
			}
			$teste = $_COOKIE;
			unset($teste['sec_session_id']);
			$this->view->setvar('itens', $itens);
		}
	}

	public function adicionaAlteracaoAction($p, $tipo = null, $produto = null){
		$this->view->setfile('itemcomanda');
		$this->view->setvar('pedido', $p);
		$this->view->setvar('id', $_COOKIE['pedido']);
		if(!empty($tipo)){
			switch($tipo){
				case 1: $t = "Pratos";
				break;				
				case 2: $t= "Lanches";
				break;
				case 3: $t = "Bebidas";
				break;
				case 4: $t = "Sobremesa";
				break;
				case 5: $t = "Outros";
				break;
			}
			$produtos = Product::all(array('conditions' => array('tipo = ? and disponivel <> 0', $t)));			
			$this->view->setvar('tipo', $t);
			$this->view->setvar('idtipo', $tipo);
			$this->view->setvar('produtos', $produtos);
			if(!empty($produto)){
				$this->view->setvar('produto', Product::find($produto));
				$post = $this->request->post();
				if(!empty($post)){
					if($post['qtd'] > 0){
						if(!isset($_COOKIE['pedido'])){
							$pedido = Request::create(array('observacao'=> NULL, 'status' => 'Anotando', 'viagem' => ($p == 1 ? 1 : 0), 'mesa'=> ($p == 1 ? NULL: $p - 2)));
							setcookie('pedido', $pedido->id, time()+36000, '/');
						}//pedido não criado (cookie id pedido não existe)
						$item = RequestItem::find_by_requests_id_and_products_id((isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id), $produto);
						if(!empty($item)){
							$item->qtd  = $item->qtd + $post['qtd'];
							$item->save();
						}//item ja cadastrado
						else{
							$item = RequestItem::create(array('requests_id' => (isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id), 'products_id' => $produto, 'qtd' => $post['qtd']));
						}//item não cadastrado
						if(!empty($item->errors)){
							$mensagem = array('success', 'Sucesso', 'Item adicionado com sucesso.');
							$this->view->setvar('viagem', ($p == 1 ? 1 : 2));
							if($p != 1){
								$this->view->setvar('mesa', $p - 2);
							}//if transforma mesa e viagem 
							$itens = RequestItem::find_all_by_requests_id((isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id));
							$this->view->setfile('alterarcomanda');
							$this->view->setvar('itens', $itens);
						}//fim if check erros item
						else{
							$mensagem = array('danger', 'Erro', 'Ocorreu ao adicionar o item.');
						}
						$this->load('Helpers\Alert', $mensagem);
					}
					else{
						$mensagem = array('danger', 'Erro', 'Ocorreu ao adicionar o item.');
						$this->load('Helpers\Alert', $mensagem);
					}	
				}//fim if check post
			}//fim if check produto
		}//fim if check tipo
	}//fim metodo

	public function AdicionaAction($p, $tipo = null, $produto = null){
		$this->view->setfile('adicionaitemcomanda');
		$this->view->setvar('pedido', $p);
		if(!empty($tipo)){
			switch($tipo){
				case 1: $t = "Pratos";
				break;				
				case 2: $t= "Lanches";
				break;
				case 3: $t = "Bebidas";
				break;
				case 4: $t = "Sobremesa";
				break;
				case 5: $t = "Outros";
				break;
			}
			$produtos = Product::all(array('conditions' => array('tipo = ? and disponivel <> 0', $t)));			
			$this->view->setvar('tipo', $t);
			$this->view->setvar('idtipo', $tipo);
			$this->view->setvar('produtos', $produtos);
			if(!empty($produto)){
				$this->view->setvar('produto', Product::find($produto));
				$post = $this->request->post();
				if(!empty($post)){
					if($post['qtd'] > 0){
						if(!isset($_COOKIE['pedido'])){
							$pedido = Request::create(array('observacao'=> NULL, 'status' => 'Anotando', 'viagem' => ($p == 1 ? 1 : 0), 'mesa'=> ($p == 1 ? NULL: $p - 2)));
							setcookie('pedido',$pedido->id, time()+36000, '/');
						}//pedido não criado (cookie id pedido não existe)
						$item = RequestItem::find_by_requests_id_and_products_id((isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id), $produto);
						if(!empty($item)){
							$item->qtd  = $item->qtd + $post['qtd'];
							$item->save();
						}//item ja cadastrado
						else{
							$item = RequestItem::create(array('requests_id' => (isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id), 'products_id' => $produto, 'qtd' => $post['qtd']));
						}//item não cadastrado
						if(!empty($item->errors)){
							$mensagem = array('success', 'Sucesso', 'Item adicionado com sucesso.');
							$this->view->setvar('viagem', ($p == 1 ? 1 : 2));
							if($p != 1){
								$this->view->setvar('mesa', $p - 2);
							}//if transforma mesa e viagem 
							$itens = RequestItem::find_all_by_requests_id((isset($_COOKIE['pedido'])? $_COOKIE['pedido']:$pedido->id));
							$this->view->setfile('abrircomanda');
							$this->view->setvar('itens', $itens);
						}//fim if check erros item
						else{
							$mensagem = array('danger', 'Erro', 'Ocorreu ao adicionar o item.');
						}
						$this->load('Helpers\Alert', $mensagem);
					}
					else{
						$mensagem = array('danger', 'Erro', 'Ocorreu ao adicionar o item.');
						$this->load('Helpers\Alert', $mensagem);
					}	
				}//fim if check post
			}//fim if check produto
		}//fim if check tipo
	}//fim metodo



	public function removeAction($p, $id){
		$this->view->setfile('abrircomanda');
		$this->view->setvar('viagem', ($p == 1 ? 1 : 2));
		if($p != 1){
			$this->view->setvar('mesa', $p - 2);
		}
		if(isset($_COOKIE['pedido'])){
			$item = RequestItem::find_by_requests_id_and_products_id($_COOKIE['pedido'], $id);
			if(empty($item->errors) and !empty($item)){
				$item->delete();
				$mensagem = array('success', 'Sucesso', 'Item excluido com sucesso.');			
			}
			else{			
				$mensagem = array('danger', 'Erro', 'Ocorreu um erro ao excluir o item.');
			}
			$this->load('Helpers\Alert', $mensagem);
			if(isset($_COOKIE['pedido'])){
				$itens = RequestItem::find_all_by_requests_id($_COOKIE['pedido']);
				$this->view->setvar('itens', $itens);
			}
		}
		else{
			$mensagem = array('danger', 'Erro', 'Ocorreu um erro ao excluir o item.');
			$this->load('Helpers\Alert', $mensagem);
		}
	}

	public function removeItemAction($p, $id){
		$this->view->setfile('alterarcomanda');
		$this->view->setvar('viagem', ($p == 1 ? 1 : 2));
		if($p != 1){
			$this->view->setvar('mesa', $p - 2);
		}
		if(isset($_COOKIE['pedido'])){
			$item = RequestItem::find_by_requests_id_and_products_id($_COOKIE['pedido'], $id);
			if(empty($item->errors) and !empty($item)){
				$item->delete();
				$mensagem = array('success', 'Sucesso', 'Item excluido com sucesso.');			
			}
			else{			
				$mensagem = array('danger', 'Erro', 'Ocorreu um erro ao excluir o item.');
			}
			$this->load('Helpers\Alert', $mensagem);
			if(isset($_COOKIE['pedido'])){
				$itens = RequestItem::find_all_by_requests_id($_COOKIE['pedido']);
				$this->view->setvar('itens', $itens);
			}
		}
		else{
			$mensagem = array('danger', 'Erro', 'Ocorreu um erro ao excluir o item.');
			$this->load('Helpers\Alert', $mensagem);
		}
	}

	public function refazAction(){
		$this->view->setfile('listaativa');
		$pedidos = Request::all(array('conditions' => 'status = "Aberta"'));
		$this->view->setvar('pedidos', $pedidos);
		$username = "root";
		$password = "";
		$conn = new PDO("mysql:host=localhost;dbname=major", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare('UPDATE requests SET viagem = :viagem, mesa = :mesa WHERE id = :id');
		$stmt->execute(array(
			':id'   => $_COOKIE['pedido'],
			':mesa' =>(empty($_COOKIE['mesa']) ? NULL : $_COOKIE['mesa']),
			':viagem' => $_COOKIE['viagem'],
		));
		$pedido = Request::find($_COOKIE['pedido']);
		if(isset($_COOKIE['observacao'])){
			$pedido->observacao = $_COOKIE['observacao'];
		}
		else{
			$pedido->observacao = NULL;
		}
		$itens = RequestItem::delete_all(array('conditions' => array('requests_id' => $pedido->id)));
		$teste = $_COOKIE;
		unset($teste['sec_session_id']);
		unset($teste['pedido']);
		unset($teste['viagem']);
		unset($teste['mesa']);		
		unset($teste['observacao']);
		$prod = array_keys($teste);
		while(!empty($teste)){
			$novo = RequestItem::create(array('requests_id'=>$pedido->id, 'products_id' => (int)array_shift($prod), 'qtd'=> (int)array_shift($teste)));
		}
		if (!empty($_COOKIE)){
			foreach ($_COOKIE as $key => $value){
				setcookie($key, $value, time() - 3600, '/');
			}
		} 
		$pedido->save();
	}

	public function consultaAction($id){
		$this->view->setfile('consultaritens');	
		$pedido = Request::find($id);
		if(!isset($pedido->errors) and empty($pedido->errors)){
			$this->view->setvar('id',$id);
			$itens = RequestItem::find_all_by_requests_id($id);
			$this->view->setvar('itens', $itens);
		}
	}

	public function listaAction(){
		$this->view->setfile('todascomandas');
		$pedidos = Request::all(array('conditions' => 'status != "Anotando"', 'order' => 'id desc'));
		$this->view->setvar('pedidos', $pedidos);
	}

	public function consultarAction($id){
		$this->view->setfile('consultarcomanda');
		$pedido = Request::find($id);
		if(!isset($pedido->errors) and empty($pedido->errors)){
			$this->view->setvar('pedido', $pedido);
			$itens = RequestItem::find_all_by_requests_id($id);
			$this->view->setvar('itens', $itens);
		}
	}

	public function fecharAction($id){
		$this->view->setfile('fecharcomanda');
		$this->view->setvar('id',$id);
	}

	public function pagarAction($id){

		$this->view->setfile('listaativa');
		$pedido = Request::find($id);
		if(empty($pedido->errors)){
			$pedido->status = "Paga";
			$pedido->save();
			$mensagem = array('success', 'Sucesso', 'Comanda paga com sucesso.');
		}
		else{
			$mensagem = array('danger', 'Erro', 'Erro ao pagar.');
		}
		$this->load('Helpers\Alert', $mensagem);
		$pedidos = Request::all(array('conditions' => 'status = "Aberta"', 'order' => 'id desc'));
		$this->view->setvar('pedidos', $pedidos);
		$this->view->setfile('listaativa');
	}

	public function cancelarAction($id){

		$pedido = Request::find($id);
		if(empty($pedido->errors)){
			$pedido->status = "Cancelada";
			$pedido->save();
			$mensagem = array('success', 'Sucesso', 'Comanda cancelada com sucesso.');
		}
		else{
			$mensagem = array('danger', 'Erro', 'Erro ao cancelar.');
		}
		$this->load('Helpers\Alert', $mensagem);
		$pedidos = Request::all(array('conditions' => 'status = "Aberta"', 'order' => 'id desc'));
		$this->view->setvar('pedidos', $pedidos);
		$this->view->setfile('listaativa');
	}
}