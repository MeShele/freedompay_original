<div class="panel panel-default">
    <div class="panel-heading">
        <i class="icon-cog"></i> {l s='FreedomPay Settings'}
    </div>
    <div class="panel-body">
        {if isset($confirmations) && !empty($confirmations)}
            <div class="alert alert-success">
                {foreach $confirmations as $conf}
                    {$conf}<br>
                {/foreach}
            </div>
        {/if}
        
        <form action="{$form_action}" method="post" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Merchant ID'}</label>
                <div class="col-lg-9">
                    <input type="text" name="merchant_id" value="{$merchant_id}" class="form-control" required>
                    <p class="help-block">{l s='Provided by FreedomPay'}</p>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Merchant Secret'}</label>
                <div class="col-lg-9">
                    <input type="password" name="merchant_secret" value="{$merchant_secret}" class="form-control" required>
                    <p class="help-block">{l s='Keep this secure'}</p>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='API URL'}</label>
                <div class="col-lg-9">
                    <select name="api_url" class="form-control" required>
                        <option value="https://api.freedompay.uz" {if $api_url == 'https://api.freedompay.uz'}selected{/if}>Uzbekistan (api.freedompay.uz)</option>
                        <option value="https://api.freedompay.kg" {if $api_url == 'https://api.freedompay.kg'}selected{/if}>Kyrgyzstan (api.freedompay.kg)</option>
                        <option value="https://api.freedompay.kz" {if $api_url == 'https://api.freedompay.kz'}selected{/if}>Kazakhstan (api.freedompay.kz)</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Test Mode'}</label>
                <div class="col-lg-9">
                    <select name="test_mode" class="form-control">
                        <option value="1" {if $test_mode}selected{/if}>{l s='Yes'}</option>
                        <option value="0" {if !$test_mode}selected{/if}>{l s='No'}</option>
                    </select>
                    <p class="help-block">{l s='Use test environment for payments'}</p>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button type="submit" name="submit_freedompay" class="btn btn-primary">
                        <i class="icon-save"></i> {l s='Save Settings'}
                    </button>
                </div>
            </div>
        </form>
        
        <div class="alert alert-info">
            <h4>{l s='Debug Information'}</h4>
            <p>{l s='Log file path:'} <code>{$module_dir}freedompay.log</code></p>
            <p>{l s='Last modified:'} {if file_exists($module_dir|cat:'freedompay.log')}{date('Y-m-d H:i:s', filemtime($module_dir|cat:'freedompay.log'))}{else}{l s='File not found'}{/if}</p>
            <a href="{$link->getAdminLink('AdminLogs')}" class="btn btn-default">
                <i class="icon-search"></i> {l s='View system logs'}
            </a>
        </div>
    </div>
</div>