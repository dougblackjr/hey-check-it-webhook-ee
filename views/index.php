<?php $this->embed('ee:_shared/table', $table); ?>

<?php if ( ! empty($table['columns']) && ! empty($table['data'])): ?>
<fieldset class="tbl-bulk-act hidden">
    <select name="bulk_action">
        <option value="">-- <?=lang('with_selected')?> --</option>
        <option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('remove')?></option>
    </select>
    <input class="btn submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
</fieldset>
<?php endif; ?>

<?php
if(isset($massRemoveUrl)) {
	$modal_vars = array(
	    'name'      => 'modal-confirm-remove',
	    'form_url'  => ee('CP/URL', $massRemoveUrl),
	    'hidden'    => array(
	        'bulk_action'   => 'remove'
	    )
	);

	$modal = $this->make('ee:_shared/modal_confirm_remove')->render($modal_vars);
	ee('CP/Modal')->addModal('remove', $modal);	
}

?>