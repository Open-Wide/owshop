{* DO NOT EDIT THIS FILE! Use an override template instead. *}

{if $errors}
<div class="message-warning">
    <h2>{'Wrong or missing rules.'|i18n( 'design/admin/shop/vatrules' )}</h2>
    <ul>
    {foreach $errors as $error}
        <li>{$error|wash}</li>
    {/foreach}
    </ul>
    {'Errors in VAT rules configuration may lead to charging wrong VAT for your products. Please fix them.'|i18n( 'design/admin/shop/vatrules' )}
</div>
{/if}

<form action={'owshop/vatrules'|ezurl} method="post" name="VatRules">

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'VAT charging rules (%rules)'|i18n( 'design/admin/shop/vatrules',, hash( '%rules', $rules|count ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $rules}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/shop/vatrules' )}" title="{'Invert selection.'|i18n( 'design/admin/shop/vatrules' )}" onclick="ezjs_toggleCheckboxes( document.VatRules, 'RuleIDList[]' ); return false;" /></th>
    <th class="tight">{'Country/region'|i18n( 'design/admin/shop/vatrules' )}</th>
    <th class="wide">{'Product categories'|i18n( 'design/admin/shop/vatrules' )}</th>
    <th>{'VAT type'|i18n( 'design/admin/shop/vatrules' )}</th>
    <th class="tight">&nbsp;</th>
</tr>

{foreach $rules as $rule sequence array( bglight, bgdark ) as $seq_color}
<tr class="{$seq_color}">
    <td><input type="checkbox" name="RuleIDList[]" value="{$rule.id}" title="{'Select rule for removal.'|i18n( 'design/admin/shop/vatrules' )}" /></td>
    <td>{if $rule.country|wash|eq('*')}{'Any'|i18n( 'design/admin/shop/vatrules' )}{else}{include uri='design:owshop/country/view.tpl' current_val=$rule.country}{/if}</td>
    <td>{$rule.product_categories_string|wash}</td>
    <td>{$rule.vat_type_name|wash} ({$rule.vat_type_object.percentage}%)</td>
    <td><a href={concat( 'owshop/editvatrule/', $rule.id)|ezurl}><img src={'edit.gif'|ezimage} width="16" height="16" alt="{'Edit'|i18n( 'design/admin/shop/vatrules' )}" title="{"Edit rule."|i18n( 'design/admin/shop/vatrules')|wash}" /></a>
</tr>
{/foreach}
</table>
{else}
<div class="block">
<p>{'There are no VAT charging rules.'|i18n( 'design/admin/shop/vatrules' )}</p>
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
<div class="button-left">
    {if $rules}
    <input class="button" type="submit" name="RemoveRuleButton" value="{'Remove selected'|i18n( 'design/admin/shop/vatrules' )}" title="{'Remove selected VAT charging rules.'|i18n( 'design/admin/shop/vatrules' )}" />
    {else}
    <input class="button-disabled" type="submit" name="RemoveRuleButton" value="{'Remove selected'|i18n( 'design/admin/shop/vatrules' )}" disabled="disabled" />
    {/if}
    <input class="button" type="submit" name="AddRuleButton" value="{'New rule'|i18n( 'design/admin/shop/vatrules' )}" title="{'Create a new VAT charging rule.'|i18n( 'design/admin/shop/vatrules' )}" />
</div>
<div class="break"></div>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>
