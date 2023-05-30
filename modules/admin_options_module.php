<?php
class woordfAdmin_options_module
{
	var $options;
	var $groups;


	public function __construct($options)
	{

		$this->set_options($options);

	}
	
	//returns fields keys
	public function set_options($options){
		if(!empty($options)){
			$this->options=array();
			$groups=array();
			$fields_without_group=array();
			foreach($options as $option){
				if(isset($option['type']) && $option['type']=='group' && $option['type']=='group' && !in_array($option,$groups)) $groups[]=$option;
				if((!isset($option['group']) || $option['group']=='') && $option['type']!='group') $fields_without_group[]=$option;
			}
			if(empty($groups) || !empty($fields_without_group)){
				$this->options[]=array(
					'name'=>'main',
					'label'=>__('Main options', WORDF_LANG),
					'fields'=>(!empty($fields_without_group))?$fields_without_group:array()
			    );
			}
			if(!empty($groups)){
				foreach($groups as $group){
					$group['fields']=array();
					foreach($options as $option){
						if(isset($option['type']) && $option['type']!='group' && isset($option['group']) && $option['group']==$group['name']){
							$group['fields'][]=$option;
						}
					}
					$this->options[]=$group;
				}
			}
		}
	}
	
	//returns fields keys
	public function get_fields_keys(){
		$result=array();
		if(!empty($this->options)){
			foreach($this->options as $group){
				if(!empty($group['fields'])){
					foreach($group['fields'] as $field) $result[]=$field['name'];
				}
			}
		}
		return $result;
	}
	
	//returns options
	public function get_options_fields(){
		return $this->options;
	}
	
	//output field by his type
	public function output_field($field){
		$key=$field['name'];
		$value = get_option($key);
		$value=($value!='')?$value:((isset($field['default']) && $field['type']!='checkbox')?$field['default']:'');
		$type=$field['type'];
		switch ($type) {
		    case 'number':
		    case 'text':
		        ?><input type="<?= $type;?>" name="<?= $key;?>" value="<?= $value;?>" /><?php
		        break;
		    case 'checkbox':
		        ?><div class="styled-toogler"><input type="<?= $type;?>" name="<?= $key;?>" id="<?= $key;?>" value="1" <?php echo ($value==1)?' checked':'';?> /><label for="<?= $key;?>">&nbsp;</label></div><?php
		        break;
		    case 'textarea':
		        ?><textarea name="<?= $key;?>"><?= $value;?></textarea><?php
		        if(isset($field['tips'])){
					?><i><?php echo $field['tips'];?></i><?php
				}
		        break;   
		    case 'select':
		    	if(isset($field['options']) && !empty($field['options'])):
		       ?>
		       <select  name="<?= $key;?>">
		       <?php 
		       		foreach($field['options'] as $option_val=>$option_label){
						?><option value="<?= $option_val;?>"<?php echo ($value==$option_val)?' selected':'';?>><?=$option_label;?></option><?php
					}
		       ?>
		       </select>
		       <?php
		        endif;
		        break;
			default:
				echo '';
		}
	}
	//routputs options fields
	public function output_options_fields(){
		if(!empty($this->options)){
			foreach($this->options as $group){
				if(!empty($group['fields'])){
?>
    				<fieldset>
	    				<legend><?php echo $group['label'];?></legend>
						<table class="form-table">
						<?php
							foreach($group['fields'] as $field){
?>
							<tr>

								<th><label for="<?php echo ($field['type']!='checkbox')?$field['name']:'';?>"><?php echo $field['label'];?></label></th>
								<td>
									<?php $this->output_field($field);?>
								</td>
							</tr>
<?php	
					}
?>

					</table>
			 </fieldset>
			<?php
				}
			}
		}

	}
	
}
?>