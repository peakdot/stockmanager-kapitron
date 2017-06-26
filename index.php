<!DOCTYPE html>
<html>
<head>
	<title>Algorithm Test!</title>
	<meta charset="utf-8">
</head>
<body>
	<form action="model/stockmodel.php"  accept-charset="utf-8" method="post">
		<input type="hidden" name="type" value="1">

		<h3>Хүснэгтийн нэр</h3>
		<input type="text" name="stock_name_1">
		<p>Баганы тоо</p>
		<input type="number" name="data_number" value="2">
		<h3>Багана 1</h3>
		<p>Баганы нэр</p>
		<input type="text" name="data_name_1">
		<p>Баганы төрөл</p>
		<select name="data_type_1">
			<option value="0">Тоо(int)</option>
			<option value="1">Тогтмол урттай тэмдэгт(char)</option>
			<option value="2">Хувьсах урттай тэмдэгт(varchar)</option>
			<option value="3">Он сар өдөр(date)</option>
		</select>
		<p>Баганы өгөгдлийн урт</p>
		<input type="number" name="data_length_1">
		<br>
		<br>
		<input type="checkbox" name="data_notnull_1">
		<label for="data_notnull_1">Хоосон байж болох эсэх/NOT NULL/</label>
		<br>
		<br>

		<h3>Багана 2</h3>
		<p>Баганы нэр</p>
		<input type="text" name="data_name_2">
		<p>Баганы төрөл</p>
		<select name="data_type_2">
			<option value="0">Тоо(int)</option>
			<option value="1">Тогтмол урттай тэмдэгт(char)</option>
			<option value="2">Хувьсах урттай тэмдэгт(varchar)</option>
			<option value="3">Он сар өдөр(date)</option>
		</select>
		<p>Баганы өгөгдлийн урт</p>
		<input type="number" name="data_length_2">
		<br>
		<br>
		<input type="checkbox" name="data_notnull_2">
		<label for="data_notnull_2">Хоосон байж болох эсэх/NOT NULL/</label>
		<br>
		<br>

		<input type="submit" name="us">
	</form>
</body>
</html>