<div class="payment_module freedompay-payment-option">
    <a href="{$payment_link}" title="{l s='Pay with FreedomPay' mod='freedompay'}" class="freedompay-payment-link">
        <img src="{$module_dir}logo.png" alt="FreedomPay" width="40" class="freedompay-logo" />
        <span class="freedompay-title">{l s='Pay with FreedomPay' mod='freedompay'}</span>
        
        {if isset($booking_total)}
            <span class="booking-total">
                {l s='Total:' mod='freedompay'} 
                {displayPrice price=$booking_total}
            </span>
        {/if}
        
        <span class="payment-arrow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H19" stroke="#3498db" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 5L19 12L12 19" stroke="#3498db" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </span>
    </a>
</div>