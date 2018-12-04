<!DOCTYPE html>
<html lang="en">
<head>
  <title>Tarea</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <style>
  .fakeimg {
      height: 200px;
      background: #aaa;
  }
  </style>
</head>
<body>
<div class="jumbotron text-center" style="margin-bottom:0">
  <h1>Tarea Desarrollo de Soluciones Cloud</h1>
  <p>usando rekognition</p> 
</div>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <a class="navbar-brand" href="#">Inicio</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">

  </div>  
</nav>
<center>
<?php
	 use Aws\Rekognition\RekognitionClient;
	if(isset($_FILES['image'])){
		 require '../../vendor/autoload.php';
	
		 $credentials = new Aws\Credentials\Credentials('/*aqui pones el key*/', '/*aqui pones el secret*/');

    $options = [
       'region'            => 'us-east-2',
        'version'           => 'latest',
		'credentials' => $credentials,
    ];
		
		$rekognition = new RekognitionClient($options);		
		$file_name = $_FILES['image']['name'];   
		$temp_file_location = $_FILES['image']['tmp_name']; 

		
		$s3 = new Aws\S3\S3Client([
			'region'  => 'us-east-2',
			'version' => 'latest',
			'credentials' => [
				'key'    => "/*aqui pones el key*/",
				'secret' => "aqui pones el secret",
			]
		]);		

		$result = $s3->putObject([
			'Bucket' => 'douglas-s3',
			'Key'    => $file_name,
			'SourceFile' => $temp_file_location			
		]);
  
	
    // Get local image
    $photo = $temp_file_location;
    $fp_image = fopen($photo, 'r');
    $image = fread($fp_image, filesize($photo));
    fclose($fp_image);
	 $result2 = $rekognition->DetectFaces(array(
       'Image' => array(
          'Bytes' => $image,
       ),
       'Attributes' => array('ALL')
       )
    );
	
		$result1 = $rekognition->recognizeCelebrities([
	
	'Image' =>['Bytes'=>file_get_contents($temp_file_location),],
'MaxLabels'=>10,
	'MinConfidence'=>20,
]);

    for ($n=0;$n<sizeof($result2['FaceDetails']); $n++){

      print 
	  'Genero: '.$result2['FaceDetails'][$n]['Gender']['Value']
      .  PHP_EOL .'<br/>'
	  .'Edad (minima):'.$result2['FaceDetails'][$n]['AgeRange']['Low']
      .  PHP_EOL .'<br/>'
      . 'Edad (maxima): ' . $result2['FaceDetails'][$n]['AgeRange']['High']
	  .  PHP_EOL .'<br/>'
	  .' Emociones: '.$result2['FaceDetails'][$n]['Emotions'][0]['Type']
      .  PHP_EOL . PHP_EOL;
    }
	if($result1['CelebrityFaces']!=null){


	
	
    
		for ($n=0;$n<sizeof($result1['CelebrityFaces']); $n++){
		
      print  '<br/>' .'Nombre: ' . $result1['CelebrityFaces'][$n]['Name']
		.  PHP_EOL . PHP_EOL;

    }
	}
	}
?>
<br/>
<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>  
</center>
<div class="jumbotron text-center" style="margin-bottom:0">
  <p></p>
</div>
</body>
</html>
