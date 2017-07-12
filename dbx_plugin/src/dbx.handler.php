<?php
if(isset($_POST['action']))
{
	if($_POST['action'] == 1)
	{
		if(isset($_POST['db_name']) && isset($_POST['table']) 
			 && isset($_POST['host'])
			&& isset($_POST['user']) && isset($_POST['pass']))
		{
			$host = $_POST['host'];
			$user = $_POST['user'];
			$pass = $_POST['pass'];
			$db = $_POST['db_name'];
			$table = $_POST['table'];			
			
					function GetBetween($var1="",$var2="",$pool)
					{
						$temp1 = strpos($pool,$var1)+strlen($var1);
						$result = substr($pool,$temp1,strlen($pool));
						$dd=strpos($result,$var2);
						if($dd == 0)
						{
							$dd = strlen($result);
						}
						return substr($result,0,$dd);
					}

					function AJAXGetData($db,$table,$host,$user,$pass)
					{
						$db = new PDO("mysql:host=$host;dbname=$db", $user, $pass
							,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));	
						$sql = "SHOW CREATE TABLE $table";
						$result = 1;
						$data = $db->query($sql);
						while($row = $data->fetch(PDO::FETCH_ASSOC))
						{
							foreach($row as $c)
							{
								$result .= $c;
							}
						}

						$result2 = GetBetween(" (","ENGINE",$result);
						$result3 = explode(",",$result2);
						$i = 1;
						foreach($result3 as $meh)
						{
							$name = GetBetween("`","`",$meh);
							$type = GetBetween("` "," ",$meh);
							if( strpos($meh,"AUTO_INCREMENT") !== false ) $auto_increment = true;
							else $auto_increment = false;
							$dates[$i] = array($name,$type,$auto_increment);
							$i++;
						}

						$len = count($result3);
						$primary = $result3[$len-1];
						if(strpos($primary,"PRIMARY") !== false)
						{
							$primary = GetBetween(" (","`)",$primary);
						}else{
							$primary = false;
						}

						$db = null;

						return $dates;
					}
					$date = AJAXGetData($db,$table,$host,$user,$pass);
					$date = json_encode($date);
					echo $date;


		}
	}else if($_POST['action'] == 0)
	{	
		if( isset($_POST['db_name']) && isset($_POST['table']) && isset($_POST['id']) &&
			isset($_POST['hostname']) && isset($_POST['user']) && isset($_POST['primary']) )
		{
			$hostname = $_POST['hostname'];
			$user = $_POST['user'];
			$pass = $_POST['pass'];
			$db = $_POST['db_name'];
			$table = $_POST['table'];
			$id = $_POST['id'];
			$primary = $_POST['primary'];
			try {
				$conn = new PDO("mysql:hostname=$hostname;dbname=$db", $user, $pass
						,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
				$sql = "DELETE FROM $table WHERE $primary='$id'";
				$smtp = $conn->prepare($sql);
			
				$smtp->execute();

				}
			catch(PDOException $e)
				{
				echo $e->getMessage();
				}	

			$conn = null;
			
		
		}else{
			echo "errorr";
		}

	}else if($_POST['action'] == 2)
	{
		if( isset($_POST['db_name']) && isset($_POST['table']) && isset($_POST['host']) 
			&& isset($_POST['user']) && isset($_POST['new_value']) && isset($_POST['primary']) &&
			isset($_POST['pass']) && isset($_POST['record_id']) && isset($_POST['column']) )
		{
		$db = $_POST['db_name'];
		$table = $_POST['table'];
		$host = $_POST['host'];
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$id = $_POST['record_id'];
		$column = $_POST['column'];
		$new_value = $_POST['new_value'];
		$primary = $_POST['primary'];	

		try {
			$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass
						,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$sql = "UPDATE $table SET $column='$new_value' WHERE $primary='$id'";
			$smtp = $conn->prepare($sql);
			
			$smtp->execute();
		
			echo $new_value;

			}
		catch(PDOException $e)
			{
			echo $e->getMessage();
			}	

		$conn = null;
		}
	}else{
		die("ERROR...");
	}
}else{
	die("ERROR.");
}

?>