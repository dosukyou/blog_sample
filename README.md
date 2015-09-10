# magento_blog_extension

<b>Module / Extension Structure</b>:

app > code

app > code > local > ICC (Company Name) > Blog (Extension Name) 

app > code > local > ICC > Blog > Block > *.php -  To structure the whole page contents.

app > code > local > ICC > Blog > controllers > *.php  - To implement and control the functional requirments

app > code > local > ICC > Blog > etc > *.xml  -  Admin Menu & Model Config XML files

app > code > local > ICC > Blog > Model > *.php  - Extending the Basic Magento Data Models 

app > code > local > ICC > Blog > sql > icc_blog_setup (named in XML config file ) > mysql4-*.php  - DB Setup Script

app > design

app > design > frontend > rwd (Package Name) > default (theme) > layout > blog.xml  - Adjusting the frontend Layout

app > design > frontend > rwd > default > template > blog > *.phtml  - Making Frontend HTML Template files

app > design > adminhtml > default > icc > layout > blog.xml  - Adjusting the backend Layout

app > etc > modules > ICC_Blog.xml -   Making the module active or not


<hr />
<b>Steps to install</b>

1. Upload all files to the root path of the magento site via ftp.

2. Remove or refresh cache

3. Go to http://[domain]/blog

<hr />
<b>Note:</b>

If you want to re-run your DB installer script (useful when you're developing),<br />
just delete the row for your module from this table: core_resource w/ below sql.

<b>DELETE from core_resource where code = 'icc_blog_setup' </b>




