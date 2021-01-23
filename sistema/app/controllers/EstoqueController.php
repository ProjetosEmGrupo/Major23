<?php 


class EstoqueController extends \HXPHP\System\Controller
{
	public function __construct($configs){
		parent::__construct($configs);	

	}

	public function indexAction(){
		$this->view->setfile('index');
	}

	public function menuAction(){//controler do visual
		$this->view->setfile('index');
	}

	public function listaAction($tipo = null){

		if(!empty($this->request->post()) or !empty($tipo)){
			if(!empty($this->request->post())){
				$post = $this->request->post();
			}else{
				switch($tipo){
					case 1: $post['tipo'] = "Pratos";
					break;				
					case 2: $post['tipo'] = "Lanches";
					break;
					case 3: $post['tipo'] = "Bebidas";
					break;
					case 4: $post['tipo'] = "Sobremesa";
					break;
					case 5: $post['tipo'] = "Outros";
					break;
				}
			}			
			$produtos = Product::all(array('conditions' => array('tipo = ? and disponivel <> 0', $post['tipo'])));
			if(empty($produtos->errors)){
				$this->view->setfile('lista');
				$this->view->setVar('produtos', $produtos);
				$this->view->setvar('tipo', $post['tipo']);
			}
			else{
				$this->load('Helpers\Alert', array('danger', 'Erro', 'Não foi possivel listar produtos.'));
			}
		}
		else{
			//erro mensagem
		}
	}

	public function excluirAction($id){
		if(!empty($id)){
			$produto = Product::find($id);
			$produto->disponivel = 0;
			$t = $produto->tipo;
			
			$produto->save();
			if(empty($produto->erros)){
				$mensagem = array('success', 'Sucesso', 'Produto excluido com sucesso.');
			}
			else{
				$mensagem = array('danger', 'Erro', 'Ocorreu um erro na exclusão do produto.');
			}
		}
		else{
			$mensagem = array('danger', 'Erro', 'Ocorreu um erro na exclusão do produto.');
			$this->view->setfile('index');
		}
		$produtos = Product::all(array('conditions' => array('tipo = ? and disponivel <> 0', $t)));
		$this->view->setfile('lista');
		$this->view->setVar('produtos', $produtos);
		$this->view->setvar('tipo', $t);
		$this->load('Helpers\Alert', $mensagem);
	}

	public function adicionaAction(){//controle visual
		$this->view->setfile('adiciona');
		if(!empty($this->request->post())){
			$post = $this->request->post(); 
			if(is_numeric($post['preco'])){
				$cadastrar = Product::create(array_merge($post, array('disponivel'=> 1)));
				if(!empty($cadastrar->errors)){
					$this->view->setfile('index');
					$this->load('Helpers\Alert', array('success', 'Sucesso', 'Produto Cadastrado.'));
				}
				else{
					$this->load('Helpers\Alert', array('danger', 'Erro', 'Produto não foi cadastrado.'));
				}
			}
			else{//preco e letra

			}
		}
	}

	public function EditarAction($id){
		if(!empty($id)){
			$post = $this->request->post();
			if(!empty($post)){
				$produto = Product::find($id);
				$produto->nome = $post['nome'];
				$produto->preco = $post['preco'];
				$produto->tipo = $post['tipo'];
				$produto->preparavel = $post['preparavel'];
				$t = $produto->tipo;
				$produto->save();
				if(empty($produto->erros)){
					$mensagem = array('success', 'Sucesso', 'Produto editado com sucesso.');
				}
				else{
					$mensagem = array('danger', 'Erro', 'Ocorreu um erro na edição do produto.');
				}
				$produtos = Product::all(array('conditions' => array('tipo = ? and disponivel <> 0', $t)));
				$this->view->setfile('lista');
				$this->view->setVar('produtos', $produtos);
				$this->view->setvar('tipo', $t);
				$this->load('Helpers\Alert', $mensagem);
			}
			else{
				$this->view->setvar('post', Product::find($id));
			}
		}
	}

}
