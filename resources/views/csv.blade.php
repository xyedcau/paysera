<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
</head>
<body>
	<a href="/">go back</a>
	<ul>
		@foreach ($datas as $data)
      <li>{{ $data }}</li>
    @endforeach
	</ul>
	<br />
	<h3>Commision</h3>
	<ul>
		@foreach ($commissions as $commission)
      <li>{{ $commission[0] }} {{ $commission[1] }}</li>
    @endforeach
	</ul>
	<a href="/">go back</a>
</body>
</html>