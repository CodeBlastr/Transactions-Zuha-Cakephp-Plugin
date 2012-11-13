<?php

App::uses('TransactionsAppModel', 'Transactions.Model');

/**
 * TransactionItem Model
 *
 * @property Product $Product
 * @property Transaction $Transaction
 * @property Customer $Customer
 * @property Contact $Contact
 * @property Assignee $Assignee
 * @property Creator $Creator
 * @property Modifier $Modifier
 */
class TransactionItem extends TransactionsAppModel {

    public $name = 'TransactionItem';
    public $displayField = 'name';

    public $validate = array(
		'price' => 'notEmpty'
    );
    


/**
 * belongsTo associations
 *
 * @var array
 */
    public $belongsTo = array(
		'Transaction' => array(
			'className' => 'Transactions.Transaction',
			'foreignKey' => 'transaction_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Assignee' => array(
			//'className' => 'Users.Assignee',
			'className' => 'Users.User',
			'foreignKey' => 'assignee_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
    );


/**
 * Creates a new cart or returns the id of the existing cart for a user, based on their user id
 * 
 * @param integer $userId The UUID of a current or future User, who is currently using a Transaction cart.
 * @return string Id of the cart in question
 */
    public function setCartId($userId) {
	  // an item was added, check for an open shopping cart.
	  $myCart = $this->Transaction->find('first', array(
		  'conditions' => array('customer_id' => $userId, 'status' => 'open')
		  ));
	  if (!$myCart) {
		  // no cart found. give them a new shopping cart.
		  $this->Transaction->create(array(
			'customer_id' => $userId,
			'status' => 'open'
		  ));
		  $this->Transaction->save();
	  } else {
		  // existing shopping cart found..  use it.
		  $this->Transaction->id = $myCart['Transaction']['id'];
	  }

	  return $this->Transaction->id;
    }
    
    
/**
 * This function ensures that a TransactionItem has it's fields filled out correctly
 * by calling upon the Model that the Item belongs to.
 * @param array $data
 * @return array
 */
    public function mapItemData($data) {

		if (empty($data['TransactionItem']['model'])) {
			throw new InternalErrorException(__('Invalid transaction item'));
		}

		App::uses($data['TransactionItem']['model'], ZuhaInflector::pluginize($data['TransactionItem']['model']) . '.Model');
		$Model = new $data['TransactionItem']['model'];

		$itemData = $Model->mapTransactionItem($data['TransactionItem']['foreign_key']);

		$itemData = Set::merge(
				$itemData,
				$data,
				array('TransactionItem' => array('transaction_id' => $this->Transaction->id))
				);

		return $itemData;
	}
    
    
/**
 * @todo check stock and cart max
 * @param array $data
 */
    public function verifyItemRequest($data) {
//	App::uses($data['TransactionItem']['model'], ZuhaInflector::pluginize($data['TransactionItem']['model']) . '.Model');
//	$Model = new $data['TransactionItem']['model'];

	return true;
    }

    
    public function statuses() {
        $statuses = array();
        foreach (Zuha::enum('ORDER_ITEM_STATUS') as $status) {
            $statuses[Inflector::underscore($status)] = $status;
        }
        return Set::merge(array('incart' => 'In Cart', 'paid' => 'Paid', 'shipped' => 'Shipped'), $statuses);
    }
    
}
