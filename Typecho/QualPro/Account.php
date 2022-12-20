<?php
include 'header.php';
include 'menu.php';
require_once __TYPECHO_ROOT_DIR__.__TYPECHO_ADMIN_DIR__.'common.php';

// 获取当前用户名
$uid = $user->__get('uid');
$name = $user->__get('name');

$option = QualPro_Plugin::getoptions();
$qauth_login_type  = $option->login_type;

$qauthUrl = QualPro_Plugin::tourl('qualpro/qauth');
$unbindingUrl = QualPro_Plugin::tourl('qualpro/unbinding');

$db = Typecho_Db::get();
$openUsers = $db->fetchAll($db->select()->from('table.qauth_user')->where( 'uid = ?', $uid));

$wechatopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "wechat_offiaccount"; } );
$miniprogramopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "wechat_miniprogram"; } );
$smsopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "sms"; } );
$qqopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "qq"; } );
$githubopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "github"; } );
$dingtalkopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "dingtalk"; } );
$weiboopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "weibo"; } );
$alipayopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "alipay"; } );
$giteeopenid = array_filter( $openUsers, function ($e) { return $e['type'] == "gitee"; } );

echo '<link rel="stylesheet" href="'.Helper::options()->pluginUrl . '/QualPro/res/qualpro.css">';
?>

<div class="main">
    <div class="body container">
        <div class="typecho-page-title">
            <h2>微信账号绑定</h2>
        </div>
        
        <div class="row typecho-page-main" role="main" style="text-align:center">
        <div>
		    <table class="typecho-list-table" style="margin-top:2rem;">
		    <?php if(in_array("wechat",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-wechat qualpro-wechat-icon"></span>&nbsp;<strong>微信</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
				        	<?php if($wechatopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=wechat_offiaccount">解绑</a>'; ?>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=wechat&login_type=binding">绑定</a>';
                            endif; ?>   
				        </div> 
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("miniprogram",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-miniprogram qualpro-miniprogram-icon"></span>&nbsp;<strong>小程序</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($miniprogramopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=wechat_miniprogram">解绑</a>'; ?>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=miniprogram&login_type=binding">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("sms",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-mobile qualpro-sms-icon"></span>&nbsp;<strong>手机号</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($smsopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=sms">解绑</a>'; ?>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=sms&login_type=binding">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("qq",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-qq qualpro-qq-icon"></span>&nbsp;<strong>QQ</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($qqopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=qq">解绑</a>'; ?>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=qq&login_type=binding">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("github",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-github qualpro-github-icon"></span>&nbsp;<strong>Github</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($githubopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=github">解绑</a>'; ?>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=github&login_type=binding">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("dingtalk",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-dingding qualpro-dingding-icon"></span>&nbsp;<strong>钉钉</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($dingtalkopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=dingtalk">解绑</a>'; ?> 
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=dingtalk&login_type=binding" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("weibo",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-weibo qualpro-weibo-icon"></span>&nbsp;<strong>微博</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($weiboopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=weibo">解绑</a>'; ?> 
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=weibo&login_type=binding" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("alipay",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-alipay qualpro-alipay-icon"></span>&nbsp;<strong>支付宝</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($alipayopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=alipay">解绑</a>'; ?> 
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=alipay&login_type=binding" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if(in_array("gitee",$qauth_login_type)):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-gitee qualpro-gitee-icon"></span>&nbsp;<strong>Gitee</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($giteeopenid):
				        	    echo '<span style="color:green;font-weight:600;">已绑定</span>';
				        	    echo '<a href="'.$unbindingUrl.'?unbinding_type=gitee">解绑</a>'; ?> 
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.$qauthUrl.'?type=gitee&login_type=binding" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    </table>
		    </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
