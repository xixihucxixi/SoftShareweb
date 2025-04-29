# SoftShareWeb

一个简单实用的【PHP+MySQL】开源软件下载与管理平台，具备用户注册/登录、软件下载与分类、软件上传与后台审批、操作日志、AJAX体验美化等功能。适合高校实验/公司内部资料共享、开源会议资源分发及个人学习进阶使用。
## 项目截图

主页演示：

![首页](blob:https://github.com/45568d14-89da-457f-9a8d-fec2b93a0a57)
## 特性

- 用户免登录浏览，注册后可下载软件资源（支持多用户）
- 软件支持分类、型号管理，支持关键字全局搜索及AJAX无刷新切换
- 管理员后台：具有软件上传/审核、分类型号管理、上传下载日志、权限多级
- 支持软件包文件上传进度条，体验丝滑
- 所有密码采用安全哈希算法存储
- 界面基于 Bootstrap4，简洁美观，移动端友好

## 安装

1. **克隆代码**

   ```bash
   git clone https://github.com/你的用户名/SoftShareHub.git
   cd SoftShareWeb

2.导入数据库结构
使用 phpMyAdmin 或 CLI 导入 docs/database.sql 文件（你需备好建表SQL）。

3.配置数据库

修改 db.php 文件，填写你的数据库主机、用户名、密码和库名。

4.确保 uploads/downloads 目录有可写权限
chmod -R 755 downloads
首次管理员账号

5.默认内置：请注册好之后手动修改is_admin为1就是管理员了
主要目录结构
assets/           # 前端CSS/JS及图片
admin/            # 后台管理代码
downloads/        # 软件包实际存储目录
index.php         # 前台首页/列表
login.php         # 登录
register.php      # 注册
download.php      # 下载逻辑（登录校验）
db.php            # 数据库连接
...

常见问题
上传失败？
检查 PHP 配置中的 upload_max_filesize 和 post_max_size
进度条无效？
用 Chrome/Edge/Firefox 等现代浏览器
登录失败？
检查数据库密码字段是否为 password_hash 值，不是明文
贡献 & 扩展
欢迎 PR，任何问题也可直接提 Issue~

许可证
MIT

致谢
本项目为开源练习范例，适当参考、二次开发请注明原作者及项目来源。

