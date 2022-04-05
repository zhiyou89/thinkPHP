<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

Route::get('api/:version/order/fenyong', 'api/:version.Order/fenYong');

Route::get('api/:version/redis/now_p', 'api/:version.Redis/saveNowDeliveryProducts');
Route::get('api/:version/redis/tomorrow_p', 'api/:version.Redis/saveTomorrowDeliverProducts');
Route::get('api/:version/redis/reality_p', 'api/:version.Redis/saveRealityDeliverProducts');

Route::get('api/:version/Setting', 'api/:version.Setting/showSetting');

Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product', 'api/:version.Product/getOne');
Route::get('api/:version/product_menu', 'api/:version.Product/showMenuByProductID');
Route::get('api/:version/product/hot', 'api/:version.Product/showIndexPageHotProduct');
Route::get('api/:version/product/search', 'api/:version.Product/searchProduct');
Route::get('api/:version/product/cart_info', 'api/:version.Product/getCartData');
Route::get('api/:version/product/sell_many', 'api/:version.Product/getSellMany');
Route::get('api/:version/product/list_one', 'api/:version.Product/getListOneIndex');

//review
Route::get('api/:version/reviews', 'api/:version.Product/showReviews');
Route::post('api/:version/save_review', 'api/:version.Review/saveReview');
Route::get('api/:version/get_review', 'api/:version.Review/getReviewsByProduct');

Route::get('api/:version/category/all/:id', 'api/:version.Category/getAllCategory');
Route::get('api/:version/category/product', 'api/:version.Category/getIndexCategory');
Route::get('api/:version/category/list_products', 'api/:version.Category/getListPageProductByCategory');

Route::get('api/:version/token/user', 'api/:version.Token/getToken');

Route::get('api/:version/address/user', 'api/:version.Address/creatOrUpdateAddress');

//菜谱
Route::get('api/:version/menu/category_by', 'api/:version.Menu/getAllMenuByCategory');
Route::get('api/:version/menu', 'api/:version.Menu/getOneMenu');
Route::get('api/:version/menu/main_info', 'api/:version.Menu/getMainProductInfo');

//订单
Route::get('api/:version/order/all', 'api/:version.Order/showOrderByStatus');
Route::post('api/:version/place_order', 'api/:version.Order/placeOrder');
Route::get('api/:version/order/one', 'api/:version.Order/getOrderInfoByID');
Route::get('api/:version/order/cancel', 'api/:version.Order/cancelOrder');
Route::get('api/:version/order/status', 'api/:version.Order/updateProductStatus');
Route::get('api/:version/order/status_sum', 'api/:version.Order/getOrderStatusSum');
Route::get('api/:version/order/del_order', 'api/:version.Order/delOrder');
//支付
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');

//退款
Route::post('api/:version/refund/wx_refund', 'api/:version.Refund/wxRefund');

//token
Route::get('api/:version/token', 'api/:version.Token/getToken');
Route::post('api/:version/check_token', 'api/:version.Token/checkToken');

//上传
Route::post('api/:version/upload', 'api/:version.Uploads/uploadImg');

Route::get('api/:version/by_product', 'api/:version.Review/getReviewsByProduct');

//user
Route::get('api/:version/user_info', 'api/:version.User/getUserInfo');
Route::get('api/:version/edit_user_info', 'api/:version.User/editUserInfo');
Route::get('api/:version/coupon', 'api/:version.User/getCoupon');
Route::get('api/:version/give_coupon', 'api/:version.User/giveCouponToUser');
Route::get('api/:version/have_coupon', 'api/:version.User/haveGetCoupon');
Route::get('api/:version/save_coupon', 'api/:version.User/saveCoupon');
Route::get('api/:version/tree', 'api/:version.User/usersTree');
Route::get('api/:version/save_superior', 'api/:version.User/saveSuperiorID');
Route::post('api/:version/telephone', 'api/:version.User/getTelephone');
Route::get('api/:version/update_telephone', 'api/:version.User/updataPhone');
Route::get('api/:version/get_telephone', 'api/:version.User/getTelephoneBySql');
Route::get('api/:version/update_psd', 'api/:version.User/updatePasswordForPersonCenter');
Route::get('api/:version/have_phone', 'api/:version.User/havePhone');
//coupon
Route::get('api/:version/update_coupon', 'api/:version.UserCoupon/updateStatus');
Route::get('api/:version/money_coupon', 'api/:version.Coupon/getCouponMoney');
Route::get('api/:version/get_coupon_list', 'api/:version.Coupon/getCouponList');

//定时任务
Route::get('api/:version/update_status', 'api/:version.Crontab/updateOrderStatus');
Route::get('api/:version/update_coupon', 'api/:version.Crontab/updateCoupon');
Route::get('api/:version/finish_order', 'api/:version.Crontab/updateFinishOrder');
Route::get('api/:version/do_fenyong', 'api/:version.Crontab/getFenyong');

//accessToken
Route::get('api/:version/get_access_token', 'api/:version.AccessToken/getAccessToken');
Route::post('api/:version/code', 'api/:version.WxCode/getWxCode');
Route::get('api/:version/redies_code', 'api/:version.WxCode/getRediesCode');
//admin
//excel
Route::post('admin/:version/sql/product', 'admin/:version.Excel/insertProductSqlData');
Route::post('admin/:version/sql/image', 'admin/:version.Excel/insertSqlToImage');
Route::post('admin/:version/sql/product_image', 'admin/:version.Excel/insertSqlToProductImage');
//product

Route::get('admin/:version/product/index', 'admin/:version.AproductController/Index');
Route::get('admin/:version/product/all', 'admin/:version.AproductController/AllProduct');
Route::get('admin/:version/product/change_sell_status', 'admin/:version.AproductController/OnSell');
Route::get('admin/:version/product/one', 'admin/:version.AproductController/OneProduct');
Route::get('admin/:version/product/category', 'admin/:version.AproductController/CategoryByProduct');
Route::get('admin/:version/product/snap_product', 'admin/:version.AproductController/snapProduct');
Route::post('admin/:version/product/save_info', 'admin/:version.AproductController/saveProductInfo');
Route::post('admin/:version/product/link_product', 'admin/:version.AproductController/linkProductByMenu');
//Order
Route::post('admin/:version/order/all', 'admin/:version.Aorder/AllOrder');

//upload
Route::post('admin/:version/upload/file', 'admin/:version.Aupload/UploadFile');
Route::post('admin/:version/upload/product_image', 'admin/:version.Aupload/uploadProductImage');

Route::get('admin/:version/printer', 'admin/:version.Printer/Printer');

//menu
Route::get('admin/:version/menu/all', 'admin/:version.Menu/AllMenuInfo');
Route::post('admin/:version/menu/update', 'admin/:version.Menu/saveMenu');



//manage
Route::get('manage/:version/index', 'manage/:version.Order/Index');
Route::get('manage/:version/set', 'manage/:version.Order/setRedis');
Route::get('manage/:version/get_order', 'manage/:version.Order/getDeliveryOrder');
Route::get('manage/:version/init_orders', 'manage/:version.Order/initOrders');
Route::get('manage/:version/my_orders', 'manage/:version.Order/myOrders');
Route::post('manage/:version/deliver_log_in', 'manage/:version.DeliveryUser/LogIN');
Route::get('manage/:version/user_order', 'manage/:version.Order/getUserOrder');
Route::get('manage/:version/order_status', 'manage/:version.Order/getOrderStatus');

Route::post('manage/:version/push_message', 'manage/:version.Wx/PushMessage');


//gzh
Route::get('gzh/scope', 'gzh/index/getScope');
Route::get('gzh/access_token', 'gzh/index/getAccessToken');
Route::get('gzh/token', 'gzh/index/checkToken');
Route::get('gzh/add_cart/:id', 'gzh/product/addCart');
Route::get('gzh/access_token', 'gzh/token/accessToken');



//store
Route::get('api/:version/sproduct', 'api/:version.Store/getStoreProductData');
Route::get('api/:version/scategory', 'api/:version.Store/getStoreCategory');
Route::get('api/:version/ssearch', 'api/:version.Store/getSearchInfo');
Route::post('api/:version/slogin', 'api/:version.Store/logIn');
Route::get('api/:version/scache', 'api/:version.Store/getCache');
Route::post('api/:version/saddcart', 'api/:version.Store/addCart');
Route::post('api/:version/scart_counts', 'api/:version.Store/getCartCounts');
Route::post('api/:version/sshow_cart', 'api/:version.Store/showCart');
Route::post('api/:version/supdate_cart', 'api/:version.Store/updateAttrInCart');
Route::post('api/:version/pay', 'api/:version.Store/pay');
Route::post('api/:version/scart_checked', 'api/:version.Store/cartChecked');
Route::post('api/:version/sorder_show', 'api/:version.Store/orderInfo');
Route::post('api/:version/sget_one_order', 'api/:version.Store/getOneOrder');
Route::post('api/:version/scancel_order', 'api/:version.Store/cancelOrder');
Route::post('api/:version/sget_good_info', 'api/:version.Store/getGoodInfo');
Route::post('api/:version/sset_address', 'api/:version.Store/setAddressToCache');
Route::post('api/:version/sget_address', 'api/:version.Store/getAddressToCache');
Route::post('api/:version/sget_one_address', 'api/:version.Store/getOneAddress');
Route::post('api/:version/sedit_address', 'api/:version.Store/editAddress');
Route::post('api/:version/sdel_address', 'api/:version.Store/delAddress');
Route::post('api/:version/schoose_address', 'api/:version.Store/chooseAddress');
Route::post('api/:version/scart_button', 'api/:version.Store/cartButton');