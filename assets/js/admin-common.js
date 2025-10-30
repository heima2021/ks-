/**
 * 管理后台通用JS函数
 */

// 返回控制台（切换到控制台tab）
function backToDashboard(){
    // 如果在iframe中，切换到控制台tab
    if(window.parent !== window && parent.layui){
        try {
            parent.layui.element.tabChange('tab', '0');
            return;
        } catch(e) {}
    }
    // 否则跳转
    window.location.href = 'main.php';
}
