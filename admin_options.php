<div class='wrap'>
	<h2>SK WP Settings Backup</h2>
        <h4>This tool might not work with any plugin, test it before using it on your website.</h4>
        <p>Plugins with <span style="font-weight:bold; background:#99ff99;">hilighted</span> name in the list use their own functions to get/set options and work better.</p>
	<form method='POST'>
	<?php if($this->message != ''){
		
		echo '<div style="border:2px #0f0 solid; margin:10px; padding:20px">'.$this->message.'</div>';
		}?>
	<?php if($this->errorMessage != ''){
		
		echo '<div style="border:2px #f00 solid; margin:10px; padding:20px"><h3>Error!</h3>'.$this->errorMessage.'</div>';
		}?>
	<fieldset style="margin-top:15px; border:2px #000 solid; padding:15px;">
		<legend style="padding:5px;border:2px #000 solid;"><b>Import settings</b></legend>
		<div style="padding:5px;border:1px #777 solid;">
			<p>
				<label style="margin-left:20px; display:block; font-weight:bold;" for="SKWPSB_IN_CODE">Paste the code here: </label>
				<textarea name="SKWPSB_IN_CODE" id="SKWPSB_IN_CODE" cols="60" rows="5"></textarea>
			</p>
			<p>
				<label style="margin-left:20px; display:block; font-weight:bold;" for="SKWPSB_IN_PLUGIN">Select destination plugin: </label>
				<select name="SKWPSB_IN_PLUGIN">
                                    <option value="">-- Select destination plugin --</option>
                                    <?php echo $this->settingsListOptions(); ?>
				</select>
			</p>
			<p style="text-align:right;">
				<input type="submit" name="SKWPSB_IN_SUBMIT" value="Save settings" />
			</p>
		</div>
	</fieldset>
	<fieldset style="margin-top:15px; border:2px #000 solid; padding:15px;">
		<legend style="padding:5px;border:2px #000 solid;"><b>Export settings</b></legend>
		<div style="padding:5px;border:1px #777 solid;">
                    <?php if(!empty($this->exportedSettings)){ ?>
			<p>
				<label style="margin-left:20px; display:block; font-weight:bold;" for="SKWPSB_EX_CODE">Copy this code: </label>
				<textarea name="SKWPSB_EX_CODE" id="SKWPSB_EX_CODE" cols="60" rows="5" onclick="this.select();"><?php echo $this->exportedSettings; ?></textarea>
                                <p>Use this code to import settings to another blog.</p>
			</p> <?php } ?>
			<p>
				<label style="margin-left:20px; display:block; font-weight:bold;" for="SKWPSB_EX_PLUGIN">Select source plugin: </label>
				<select name="SKWPSB_EX_PLUGIN">
                                    <option value="">-- Select source plugin --</option>
                                    <?php echo $this->settingsListOptions(); ?>
				</select>
			</p>
			<p style="text-align:right;">
				<input type="submit" name="SKWPSB_EX_SUBMIT" value="Get the code" />
			</p>
		</div>
	</fieldset>
	</form>
</div>