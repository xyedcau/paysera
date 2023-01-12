<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Paysera</title>
</head>
<body>
	<div>
		<form action="/upload" method="post" enctype="multipart/form-data">
			@csrf
			<div style="text-align: center">
				<label for="">Upload CSV only file</label><br /><br />
				<input type="file" name="commi" id="commi"><br /><br />
				<input type="submit" value="Submit">
			</div>
		</form>
	</div>
</body>
</html>