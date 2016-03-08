<?php
	switch ($_SERVER['HTTP_ORIGIN']) {
    	case 'http://develop.flvs.net': case 'http://learn.flvs.net':
   	 	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
   		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    	header('Access-Control-Max-Age: 1000');
    	header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    	break;
	}
	
	$servername 		= 'localhost';
	$username 			= 'root';
	$password 			= '6f7cf5431e';
	$dbname 			= 'pla';
	$method 			= $_SERVER['REQUEST_METHOD'];

	//detect the type of request
	switch ($method) {
		
	  	case 'POST':
	  		echo 'heard a post....              ';
			$data              	= file_get_contents('php://input');
			$action 			= $_GET['action'];
			
			//For updating weight of answered questions...
			//***********************************************************
			if ($action == 'weight') {
				echo 'adding weight to question....          ';
				$dataJsonDecode    	= json_decode($data, TRUE);
			    $questionID        	= $dataJsonDecode['id'];
				
				$sql = "UPDATE questions SET weight = weight+1 WHERE id = $questionID";
				postCall($servername, $username, $password, $dbname, $sql);
				
				
				echo 'updating cache....          ';
				
				$sql = "SELECT * FROM questions ORDER BY weight DESC";
				saveCache($servername, $username, $password, $dbname, $sql);
			}

			
			//For deleting of unanswered questions...
			//***********************************************************
			if ($action == 'delete') {
				echo 'deleting....          ';
				$questionID    		= json_decode($data, TRUE);
				
				$sql = "DELETE FROM unanswered WHERE id = $questionID";
				postCall($servername, $username, $password, $dbname, $sql);
			}
			
			//For submitting new unanswered questions
			//***********************************************************
			if ($action == 'submitQuestion') {
				
			    $dataJsonDecode    	= json_decode($data, TRUE);
			    $courseID         	= $dataJsonDecode['courseID'];
			    $courseID 			= print_r($courseID,true);
				$newQuestion		= addslashes($dataJsonDecode['newQuestion']);
				$lesson				= addslashes($dataJsonDecode['lesson']);
				
				
	
				$sql = "INSERT INTO unanswered (courseID, question, ownedBy) VALUES ('$courseID','$newQuestion','$lesson')";
				postCall($servername, $username, $password, $dbname, $sql);
				
				$sql = "SELECT * FROM courses WHERE courseID=$courseID";
				getCourses($servername, $username, $password, $dbname, $sql, $courseID, $newQuestion);
				
				
			}
			//For adding new courses
			//***********************************************************
			if ($action == 'addCourse') {
				echo 'adding course....          ';
				$data              	= file_get_contents('php://input');
    			$dataJsonDecode    	= json_decode($data, TRUE);
   				$teachers         	= $dataJsonDecode['teachers'];
				$courseID         	= $dataJsonDecode['courseID'];
	
				$sql = "INSERT INTO courses (courseID, teachers) VALUES ('$courseID','$teachers')";
				postCall($servername, $username, $password, $dbname, $sql);
			}
				
			//For answering questions; removing them from 'unaswered' and adding them to 'questions'
			//***********************************************************
			if ($action == 'answerQuestion') {
				echo 'adding question.....          ';
				$data              	= file_get_contents('php://input');
    			$dataJsonDecode    	= json_decode($data, TRUE);
   				$questionID         = $dataJsonDecode['questionID'];
   				$courseID         	= $dataJsonDecode['courseID'];
				$question         	= $dataJsonDecode['question'];
				$answer	        	= $dataJsonDecode['answer'];
				$keywords	      	= $dataJsonDecode['keywords'];
	
				$sql = "DELETE FROM unanswered WHERE id = $questionID";
				postCall($servername, $username, $password, $dbname, $sql);
				
				$sql = "INSERT INTO questions (keywords, question, answer, weight, courseID) VALUES ('$keywords','$question','$answer','0','$courseID')";
				postCall($servername, $username, $password, $dbname, $sql);
				
				$sql = "SELECT * FROM questions ORDER BY weight DESC";
				saveCache($servername, $username, $password, $dbname, $sql);
			}
			
			break;
			
	  	case 'GET':
			$action = $_GET['action'];
			
			//For retrieving the cached answeres for the front-end...
			//***********************************************************
			if ($action == 'getQuestions' ) {
				$json = file_get_contents('https://reinvention.flvs.net/plapp_api/v2/newapi/cache/data.json');
				$json = stripslashes($json);
				echo $_GET['callback'].'('.$json.')';
			}
			
			
			//For retrieving unanswered questions for the editor...
			//***********************************************************
			if ($action == 'getUnanswered' ) {
				getUnanswered($servername, $username, $password, $dbname);
			}
			break;
	}

	function getUnanswered($servername, $username, $password, $dbname) {
		$conn = new mysqli($servername, $username, $password, $dbname);

			$sql = "SELECT * FROM unanswered";
			
			$result = $conn->query($sql) or die($mysqli->error.__LINE__);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$temp[] = $row;
				};			
				$json = json_encode($temp);
				echo $_GET['callback'].'('.$json.')';
				
			};
		$conn->close();	
	};
	
	function postCall($servername, $username, $password, $dbname, $sql) {
		$conn = new mysqli($servername, $username, $password, $dbname);
			echo $sql.'         ';
			$result = $conn->query($sql) or die($mysqli->error.__LINE__);
			echo 'done!';
		$conn->close();	
	};
	
	function saveCache($servername, $username, $password, $dbname, $sql) {
		$conn = new mysqli($servername, $username, $password, $dbname);
		
		$result = $conn->query($sql) or die($mysqli->error.__LINE__);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$temp[] = $row;
				};			
				$json = json_encode($temp);
				echo $_GET['callback'].'('.$json.')';
				
				$f = file_put_contents('cache/data.json', json_encode($temp));

				if($f){ echo 'done'; }else{ echo 'broken!'; }
				
			};
		$conn->close();	
		
	};
	
	function getCourses($servername, $username, $password, $dbname, $sql, $courseID, $newQuestion) {
		$conn = new mysqli($servername, $username, $password, $dbname);
		
		$result = $conn->query($sql) or die($mysqli->error.__LINE__);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$teachers =  explode(',', $row['teachers']);

				};			
				foreach ($teachers as &$teacher) {
					$to = $teacher;
					$header = 'From: FLVS';
					$subject = 'New Question from PLA';
					$body = 'New Question for course: '.$courseID.'.  '.$newQuestion.'  Answer the question at: https://reinvention.flvs.net/pla/review/index.htm';
					
					if (mail($to, $subject, $body, $header)) {
						echo('Message successfully sent');
					} else {
						echo('Message delivery failed...');
					}
			
				}				
			};
		$conn->close();	
		
	};

?>

