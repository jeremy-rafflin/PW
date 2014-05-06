<?php
namespace Pw/RecursiveElement;
//classe d'un élément récurcif
class RecursiveElement {
	
	//tableau static qui permet de stocker l'ensemble des élément trier par ordre d'élément/sous élément
	private static $_arrayElement = array();
	
	//nom de le la colonne id de l'élément dans la base de donnée
	private static $_nameId;
	
	//nom de la colonne permettant de trouver le parent de l'élément de l'élément dans la base de donnée
	private static $_nameParentId;
	
	//permet de stocké l'élément
	public $element;
	
	//permet de stocker les enfants de l'élément
	public $childs = array();
	
	//permet de stocker le parent de l'élément (null par défaut quand il n'a pas de parent)
	public $parent=null;
	
	/**
	 * constructeur d'un élément récurcif
	 * @param unknown_type $elem
	 */
	public function __construct($elem){
		$this->element = $elem;
	}
	
	/**
	 * permet de mettre les éléments récurcif dans la class et de les trier
	 * @param unknown_type $elements
	 * @param String $columIdName
	 * @param String $columParentIdName
	 */
	public static function setElements($elements = array(), $columIdName, $columParentIdName) {
		PW_RecursiveElement::$_arrayElement = array();//on réinitialiser le tableau desfois qu'on l'ai déjà utiliser au par avant
		
		self::$_nameId = $columIdName;
		self::$_nameParentId = $columParentIdName;
		
		//on liste tous les éléments et on les met dans la liste
		foreach ($elements as $element) {
			$elementATrier = new PW_RecursiveElement($element);
			$id = PW_RecursiveElement::$_nameId;//pour avoir l'id de l'élément
			PW_RecursiveElement::$_arrayElement[$elementATrier->element->$id] = $elementATrier;
		}
		
		//maintenant on trie le tableau
		foreach (PW_RecursiveElement::$_arrayElement as $element){
			//si l'id du parent est null c'est un élément de base, on le laisse au 1er niveau sinon on le traite
			$idParent = PW_RecursiveElement::$_nameParentId;//pour avoir l'id du parent de l'élément
			if($element->element->$idParent != null && $element->element->$idParent != 0 && $element->element->$idParent != '') {
				//s'il à parent on recherche le parent
				$parent = PW_RecursiveElement::getElement($element->element->$idParent, PW_RecursiveElement::$_arrayElement);
				//une fois le parent trouvé, on supprime l'élément du 1er niveau et on met qu'il est enfant de son père et qui est son père
				$element->parent = $parent;
				$parent->childs[] = $element;
				unset(PW_RecursiveElement::$_arrayElement [$element->element->$id]);
			}
			
		}
	}
	
	/**
	 * permet de renvoyé l'adresse d'un élément du tableau d'objet récurcif
	 * @param String $id
	 * @param array of unknown_type $tabElement
	 */
	private static function getElement($id, &$tabElement) {
		foreach ($tabElement as $element){
			$idE = PW_RecursiveElement::$_nameId;//pour avoir l'id de l'élément
			if($element->element->$idE == $id) {//on a trouvé le bon on retourne l'adresse de cette élément
				return $element;
			}
			else {//si on a pas trouvé le bon
				if (count($element->childs)>0) {//on recherche dans les éléments enfant (s'il en as)
					$elementTrouver = PW_RecursiveElement::getElement($id, $element->childs);
					if($elementTrouver) 
						return $elementTrouver;
				}
			}
		}
	}
	
	/**
	 * permet de retourner les éléments dans l'ordre parent->enfant->autre parent->enfants .... dans un tableau de la forme id => element
	 * @param array $array
	 * @param String $separatorSousElement
	 */
	public static function getElementInArray(&$array, $nameAttributeInArrray, $separatorSousElement ='------') {
		//pour chaque élément
		foreach (PW_RecursiveElement::$_arrayElement as $element) {
			$idE = PW_RecursiveElement::$_nameId;//pour avoir l'id de l'élément
			$array[$element->element->$idE] = $element->element->$nameAttributeInArrray;
			//si l'élément à des enfant on traite les enfants
			if (count($element->childs)>0) {
				self::getChildElementInArray($element->childs, $array, $nameAttributeInArrray, $separatorSousElement);
			}
		}
	}
	
	/**
	 * permet de mettre les élément enfant d'un élément dans un tableau de la forme id => element
	 * @param array of unknown_type $childsArray
	 * @param array $array
	 * @param String $separatorSousElement
	 */
	private static function getChildElementInArray($childsArray, &$array, $nameAttributeInArrray, $separatorSousElement) {
		foreach ($childsArray as $element) {
			$idE = PW_RecursiveElement::$_nameId;//pour avoir l'id de l'élément
			$array[$element->element->$idE] = $separatorSousElement.' '.$element->element->$nameAttributeInArrray;
			//si l'élément à des enfant on traite les enfants
			if (count($element->childs)>0) {
				$separatorSousElement.= $separatorSousElement;
				self::getChildElementInArray($element->childs, $array, $nameAttributeInArrray, $separatorSousElement);
			}
		}
	}
	
	/**
	 * permet de retourner les éléments dans l'ordre parent->enfant->autre parent->enfants .... dans un tableau de la forme id => array('element' =>element, 'niveau'+>niv)
	 * @param array $array
	 * @param String $separatorSousElement
	 */
	public static function getElementWithNiveauInArray(&$array, $nameAttributeInArrray) {
		//pour chaque élément
		foreach (PW_RecursiveElement::$_arrayElement as $element) {
			$idE = PW_RecursiveElement::$_nameId;//pour avoir l'id de l'élément
			$array[$element->element->$idE] = array('element'=>$element->element, 'niveau'=>0);
			//si l'élément à des enfant on traite les enfants
			if (count($element->childs)>0) {
				self::getChildElementWithNiveauInArray($element->childs, $array, $nameAttributeInArrray, 1);
			}
		}
	}
	
	/**
	 * permet de mettre les élément enfant d'un élément dans un tableau de la forme id => element
	 * @param array of unknown_type $childsArray
	 * @param array $array
	 * @param String $separatorSousElement
	 */
	private static function getChildElementWithNiveauInArray($childsArray, &$array, $nameAttributeInArrray, $niveau) {
		foreach ($childsArray as $element) {
			$idE = PW_RecursiveElement::$_nameId;//pour avoir l'id de l'élément
			$array[$element->element->$idE] = array('element'=>$element->element, 'niveau'=>$niveau);
			//si l'élément à des enfant on traite les enfants
			if (count($element->childs)>0) {
				$niveau++;
				self::getChildElementInArray($element->childs, $array, $nameAttributeInArrray, $niveau);
			}
		}
	}
}