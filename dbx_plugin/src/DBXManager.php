<?php namespace src;

use \PDO;
class DBXManager
{	
	private $hostname;
	private $username;
	private $password;
	private $instance;
	private $theme;
	
	function __construct($hostname,$username,$password,$theme = "impact")
	{    		
			echo "<link rel='stylesheet' type='text/css' href='dbx_plugin/src/style/dataTables.tableTools.css'>";
			echo "<link rel='stylesheet' type='text/css' href='dbx_plugin/src/style/themes/$theme/dbx_$theme.css'>";
			echo "<script type='text/javascript' src='dbx_plugin/src/js/jquery.min.js'></script>";
			echo "<script type='text/javascript' src='dbx_plugin/src/js/jquery.dataTables.min.js'></script>";
			echo "<script type='text/javascript' src='dbx_plugin/src/js/dataTables.tableTools.min.js'></script>";
			echo "<script type='text/javascript' src='dbx_plugin/src/js/dataTables.responsive.js'></script>";
			echo "<script type='text/javascript' src='dbx_plugin/src/js/dbx.js'></script>";
			echo "<script type='text/javascript' src='dbx_plugin/src/js/jquery.mobile-1.4.5.min.js'></script>";
			echo "<link rel='stylesheet' type='text/css' href='dbx_plugin\src\style\jquery.mobile-1.4.5.min.css'>";
			echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";

			$this->theme = $theme;
			$this->hostname = $hostname;
			$this->username = $username;
			$this->password = $password;
			$this->instance = 0;

	}

	function __destruct()
	{
		$db = null;
	}

	public function ShowHTMLTable($db_name,$table,$admin_mode = false)
	{	
		$this->instance = $this->instance + 1;
		try{
		$db = new PDO("mysql:host=$this->hostname;dbname=$db_name", $this->username, $this->password
					,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$q = $db->prepare("DESCRIBE $table");	
			$q->execute();
								echo "<script type='text/javascript'>";
    								echo "	$(document).ready(function () {";
        							echo "		$('#table' + $this->instance).dataTable({'dom': 'T<&quot;clear&quot;>frtip','tableTools': { 'sSwfPath': 'dbx_plugin/src/swf/copy_csv_xls_pdf.swf' },'pagingType': 'full_numbers','ordering': 'true'});";
    								echo "	});";
								echo "</script>";	
		
				echo "<table id='table".$this->instance."' align='center' class='responsive'>";
				echo "<thead>";
				echo "<tr>";
			$table_field = $q->fetchAll(PDO::FETCH_COLUMN);
			
				foreach($table_field as $field)
				{
					echo "<th class='sorting'>" . $field . "</th>";
				}
				echo "</tr>";
				echo "</thead>";
			$sql = "SELECT * FROM $table";
				echo "<tbody>";
			foreach( $db->query($sql) as $row )
			{	
				$count = count($row)/2;
				echo "<tr id='table$this->instance"."B$row[0]'>";
				if($admin_mode)
				{	
					$idtbs = "table" . $this->instance . "B" . $row[0];
					echo "<td ondblclick='edit_mode_update(this.innerText,this,&quot;$db_name&quot;,&quot;$table&quot;,&quot;$this->hostname&quot;,&quot;$this->username&quot;,&quot;$this->password&quot;);' class='table$this->instance"."B$row[0]"."B0'>" . $row[0] . "<img src='dbx_plugin/src/style/images/delete.png' class='admin_mode_buttons deletebuttons' OnClick='delete_popup(&quot;$db_name&quot;,&quot;$table&quot;,&quot;$row[0]&quot;,&quot;$this->hostname"."&quot;,&quot;$this->username"."&quot;,&quot;$this->password"."&quot;);'>" . "</td>";
					for($i=1;$i<$count;$i++)
					{
						echo "<td ondblclick='edit_mode_update(this.innerText,this,&quot;$db_name&quot;,&quot;$table&quot;,&quot;$this->hostname&quot;,&quot;$this->username&quot;,&quot;$this->password&quot;);' class='table$this->instance"."B$row[0]"."B$i'>" . $row[$i] . "</td>";
					}
				}else{
					for($i=0;$i<$count;$i++)
					{
						echo "<td>" . $row[$i] . "</td>";
					}
				}
				echo "</tr>";
			}
				echo "</tbody>";
			echo "</table>";
	
			echo "<script type='text/javascript'>document.getElementById('table".$this->instance."').width='100%';</script>";
								if($admin_mode)
								{	
									echo "<script type='text/javascript'>";
									echo "$('#table" . $this->instance. " tbody').on('click', 'img.deletebuttons', function(){";
									echo "$('#table" . $this->instance . "').DataTable().row($(this).parents('tr')).remove().draw();";
									echo "});";
									echo "</script>";

									echo "<script type='text/javascript'>";
									echo "if($(document).width() < 961){";
									echo "$('td').on('taphold', function(){edit_mode_update(this.innerText,this,'$db_name','$table','$this->hostname','$this->username','$this->password'); });";
									echo "}";
									echo "</script>";
								}
		$db = null;
		}catch(PDOException $e){
			echo $e->getMessage();
		}

		
	}

	function ShowDBS()
	{
		try {
  			$dbh = new PDO("mysql:host=$this->hostname;dbname=mysql", $this->username, $this->password
						,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

			$sql = "SHOW DATABASES";
			$databases = $dbh->query($sql);
			$DBList = array();
			while( $row = $databases->fetch(PDO::FETCH_NUM))
			{
				$DBList[] = $row[0];
			}

			return $DBList;
    		}
		catch(PDOException $e)
    		{
    			echo "Error:Could not connect to database.";
    		}

	}

	function ShowTables($database)
	{
		try {
  			$dbh = new PDO("mysql:host=$this->hostname;dbname=$database", $this->username, $this->password
						,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$sql = "SELECT table_name FROM information_schema.tables WHERE table_schema='$database'";
			$tables = $dbh->query($sql);
			$TBList = array();
			while( $row = $tables->fetch(PDO::FETCH_NUM))
			{
				$TBList[] = $row[0];
			}

			return $TBList;
			}

		catch(PDOException $e)
    		{
    			echo "Error:Could not connect to database.";
    		}

	}

	function ShowDataFromTable($database,$table)
	{
			try {
  			$dbh = new PDO("mysql:host=$this->hostname;dbname=$database", $this->username, $this->password
						,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$sql = "SELECT * FROM $table";
			$databases = $dbh->query($sql);
		
			while( $row = $databases->fetchAll(PDO::FETCH_ASSOC))
			{
				$DBList = $row;
			}

			return $DBList;
			}

		catch(PDOException $e)
    		{
    			echo "Error:Could not connect to database.";
    		}
	}

	function ResetTable($database,$table)
	{
			try {
  			$dbh = new PDO("mysql:host=$this->hostname;dbname=$database", $this->username, $this->password
						,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$sql = "DELETE FROM $table";
			$dbh->exec($sql);
			$dbh = null;
			}
		catch(PDOException $e)
    		{
    			echo "Error:Could not connect to database.";
    		}
	}
}

?>