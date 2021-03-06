<div class="transactions index">
	<table cellpadding="0" cellspacing="0">
        <thead>
	    <tr>
			<th><?php echo $this->Paginator->sort('Customer.last_name', 'Customer');?></th>
    		<th><?php echo $this->Paginator->sort('Transaction.created', 'Date');?></th>
			<th><?php echo $this->Paginator->sort('Transaction.status', 'Status');?></th>
			<th><?php echo $this->Paginator->sort('Transaction.total', 'Total');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
    	</tr>
        </thead>
        <tbody>
    	<?php
            foreach ($transactions as $transaction) :
                $canUse = false;
                if(!empty($transaction['TransactionItem']) && isset($transaction['TransactionItem'][0])){
                    $canUse = $transaction['TransactionItem'][0]['status']='paid' && $transaction['Transaction']['processor_response'] == 'Approved';
                    $itemId = $transaction['TransactionItem'][0]['id'];

                }
        ?>
    	<tr>
    		<td>
    			<?php
				if (!empty($transaction['Customer']['full_name'])) {
					echo $this->Html->link($transaction['Customer']['full_name'], array('plugin' => 'users', 'controller' => 'users', 'action' => 'view', $transaction['Customer']['id']));
				} elseif (!empty($transaction['TransactionAddress'][0]['first_name']) || !empty($transaction['TransactionAddress'][0]['last_name'])) {
					echo __('%s %s', $transaction['TransactionAddress'][0]['first_name'], $transaction['TransactionAddress'][0]['last_name']);
				} else {
					echo '<span class="text-muted">guest</span>';
				}
				?>
    			<div><?php echo $transaction['Customer']['email'] ?></div>
    		</td>
    		<td><?php echo ZuhaInflector::datify($transaction['Transaction']['created']); ?>&nbsp;</td>
    		<td>
				<?php
				switch ($transaction['Transaction']['status']) {
					case 'open':
						$statusLabelClass = 'default';
						break;
					case 'paid':
						$statusLabelClass = 'success';
						break;
					default:
						$statusLabelClass = 'info';
						break;
				}
				?>
				<span class="label label-<?= $statusLabelClass ?>"><?= $transaction['Transaction']['status'] ?></span>
			</td>
    		<td><?php echo '$' . ZuhaInflector::pricify($transaction['Transaction']['total']); ?>&nbsp;</td>
    		<td class="actions">
    			<?php echo $this->Html->link(__('View'), array('action' => 'view', $transaction['Transaction']['id']), array('class' => 'btn btn-default')); ?>
    			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $transaction['Transaction']['id']), array('class' => 'btn btn-warning')); ?>
    			<?php if($canUse) echo $this->Html->link(__('Post'), array('plugin'=>'classifieds','controller'=>'classifieds','action' => 'post', $itemId), array('class' => 'btn btn-warning')); ?>
    			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $transaction['Transaction']['id']), array('class' => 'btn btn-xs btn-danger'), __('Are you sure you want to delete # %s?', $transaction['Transaction']['id'])); ?>
    		</td>
	    </tr>
	    <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $this->Element('paging'); ?>
</div>

<?php
// set the contextual breadcrumb items
$this->set('context_crumbs', array('crumbs' => array(
	$this->Html->link(__('eCommerce Dashboard'), array('plugin' => 'transactions', 'controller' => 'transactions', 'action' => 'dashboard')),
	$page_title_for_layout,
)));

// set the contextual menu items
$named =  array('limit' => $this->Paginator->counter('{:count}')) + array_reverse($this->request->named);
$this->set('context_menu', array('menus' => array(
    array(
		'heading' => 'Products',
		'items' => array(
			$this->Html->link(__('Dashboard'), array('plugin' => 'products', 'controller' => 'products', 'action' => 'dashboard')),
			//http://discoverywoods.buildrr.com/admin/transactions/transactions/index/sort:Transaction.created/direction:desc/limit:1000/filter:status%3Apaid.csv
			$this->Html->link(__('Download %s Transactions', $this->Paginator->counter('{:count}')), array('plugin' => 'transactions', 'controller' => 'transactions', 'action' => 'index', 'ext' =>'csv') + $named),
			)
		),
	)));
