<?php
/**
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://ez.no/Resources/Software/Licenses/eZ-Business-Use-License-Agreement-eZ-BUL-Version-2.1 eZ Business Use License Agreement eZ BUL Version 2.1
 * @version 5.2.0
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$shopIni = eZINI::instance('shop.ini');

$tpl = eZTemplate::factory();
$tpl->setVariable( "module_name", 'owshop' );

$orderID = $http->sessionVariable( 'MyTemporaryOrderID' );

$order = eZOrder::fetch( $orderID );
if ( !is_object( $order ) )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( $order instanceof eZOrder )
{
    if ( $http->hasPostVariable( "ConfirmOrderButton" ) )
    {
        $order->detachProductCollection();
        $ini = eZINI::instance();
        if ( $ini->variable( 'ShopSettings', 'ClearBasketOnCheckout' ) == 'enabled' )
        {
            $basket = eZBasket::currentBasket();
            $basket->remove();
        }
        $module->redirectTo( '/owshop/checkout/' );
        return;
    }

    if ( $http->hasPostVariable( "CancelButton" ) )
    {
        $order->purge( /*$removeCollection = */ false );
        $module->redirectTo( '/owshop/basket/' );
        return;
    }

    $tpl->setVariable( "order", $order );
}

$basket = eZBasket::currentBasket();
$basket->updatePrices();

$operationResult = eZOperationHandler::execute( 'owshop', 'confirmorder', array( 'order_id' => $order->attribute( 'id' ) ) );

switch( $operationResult['status'] )
{
    case eZModuleOperationInfo::STATUS_CONTINUE:
    {
        if ( $operationResult != null &&
             !isset( $operationResult['result'] ) &&
             ( !isset( $operationResult['redirect_url'] ) || $operationResult['redirect_url'] == null ) )
        {
            if($shopIni->hasVariable('ShopSettings', 'PassConfirmOrder') &&
                $shopIni->variable('ShopSettings', 'PassConfirmOrder') == 'true') {
                $order->detachProductCollection();
                $ini = eZINI::instance();
                if ($ini->variable('ShopSettings', 'ClearBasketOnCheckout') == 'enabled') {
                    $basket = eZBasket::currentBasket();
                    $basket->remove();
                }
                $module->redirectTo('/owshop/checkout/');
                return;
            }
            $order = eZOrder::fetch( $order->attribute( 'id' ) );
            $tpl->setVariable( "order", $order );

            $Result = array();
            $Result['content'] = $tpl->fetch( "design:shop/confirmorder.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'kernel/shop', 'Confirm order' ) ) );
        }
    }break;

    case eZModuleOperationInfo::STATUS_HALTED:
    case eZModuleOperationInfo::STATUS_REPEAT:
    {
        if (  isset( $operationResult['redirect_url'] ) )
        {
            $module->redirectTo( $operationResult['redirect_url'] );
            return;
        }
        else if ( isset( $operationResult['result'] ) )
        {
            $result = $operationResult['result'];
            $resultContent = false;
            if ( is_array( $result ) )
            {
                if ( isset( $result['content'] ) )
                {
                    $resultContent = $result['content'];
                }
                if ( isset( $result['path'] ) )
                {
                    $Result['path'] = $result['path'];
                }
            }
            else
            {
                $resultContent = $result;
            }
            $Result['content'] = $resultContent;
        }
    }break;
    case eZModuleOperationInfo::STATUS_CANCELLED:
    {
        $Result = array();
        if ( isset( $operationResult['result']['content'] ) )
            $Result['content'] = $operationResult['result']['content'];
        else
            $Result['content'] = ezpI18n::tr( 'kernel/shop', "The confirm order operation was canceled. Try to checkout again." );

        $Result['path'] = array( array( 'url' => false,
                                        'text' => ezpI18n::tr( 'kernel/shop', 'Confirm order' ) ) );
    }

}

/*
$Result = array();
$Result['content'] = $tpl->fetch( "design:shop/confirmorder.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/shop', 'Confirm order' ) ) );
*/
?>
