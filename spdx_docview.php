<?php
if(array_key_exists('doc_id',$_GET)){
$spdxId = $_GET["doc_id"];
}
else{
$spdxId = $_POST["doc_id"];
}
if(array_key_exists('action',$_POST)){
    /*$url = "http://$_SERVER[HTTP_HOST]/spdx_docupdate.php";
	$r = new HttpRequest($url, HttpRequest::METH_POST);
	try {
    echo $r->send();
	} catch (HttpException $ex) {
		//echo $ex;
	}
	*/
	if($_POST["action"] == "update"){
				 $docs = "UPDATE spdx_docs " .
					  " SET document_comment='" .  $_POST["document_comment"] ."',".
					  " spdx_version='" .  $_POST["spdx_version"] ."',".
					  " data_license='" .  $_POST["data_license"] ."',".
					  " updated_at=now()".
					  " WHERE id=" . $_POST["doc_id"];
					  
				 $creators = "UPDATE creators 
				              set creator = '" .  $_POST["creator"] ."',
							  creator_comments = '" .  $_POST["creator_comments"] ."'  
							  where spdx_doc_id =" . $_POST["doc_id"];
							  
				 $packages = "UPDATE packages, doc_file_package_associations
				              set packages.package_name ='". $_POST["package_name"] ."', 
							  packages.package_version ='". $_POST["package_version"] ."', 
							  packages.package_download_location ='". $_POST["package_download_location"] ."', 
							  packages.package_summary ='". $_POST["package_summary"] ."', 
							  packages.package_file_name ='". $_POST["package_file_name"] ."', 
							  packages.package_supplier ='". $_POST["package_supplier"] ."', 
							  packages.package_originator ='". $_POST["package_originator"] ."', 
							  packages.package_verification_code ='". $_POST["package_verification_code"] ."', 
							  packages.package_description ='". $_POST["package_description"] ."', 
							  packages.package_copyright_text ='". $_POST["package_copyright_text"] ."', 
							  packages.package_license_concluded ='". $_POST["package_license_concluded"] ."' 
                              where doc_file_package_associations.package_id = packages.id 
							  and doc_file_package_associations.spdx_doc_id=". $_POST["doc_id"];							  
					  
				$con=mysqli_connect("localhost","root","","spdx");
				// Check connection
				if (mysqli_connect_errno())
				  {
				  echo "Failed to connect to MySQL: " . mysqli_connect_error();
				  }
				mysqli_query($con,$docs);
				mysqli_query($con,$creators);
				mysqli_query($con,$packages);
				mysqli_close($con);
	}
	
}
$con=mysqli_connect("localhost","root","","spdx");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }


$query = "SELECT DISTINCT " .
                 "spdx_docs.id, spdx_version, data_license, document_comment, spdx_docs.updated_at,creator, creators.created_at, creator_comments, package_name, package_version,
				 package_download_location, package_summary, package_file_name, package_supplier, package_originator, package_checksum, 
				 package_verification_code, package_description,package_copyright_text, package_license_declared, package_license_concluded, 
				 package_license_info_from_files" .
             " FROM ".
                 "spdx_docs" .
             " INNER JOIN " .
                 "creators"  .
             " ON " . 
                 "spdx_docs.id = creators.spdx_doc_id" .
             " INNER JOIN " .
                 "doc_file_package_associations".
             " ON ".
                 "spdx_docs.id = doc_file_package_associations.spdx_doc_id" .
             " INNER JOIN " .
                 "packages" .
             " ON " .
                 "doc_file_package_associations.package_id = packages.id" .
			 " WHERE spdx_docs.id = " . $spdxId; 
				 

$result = mysqli_query($con,$query);

if($result != false){
    $hasRow = false;  
	while($rowVal = mysqli_fetch_assoc($result)) {
			$row = $rowVal;
			$hasRow = true;
	}
}	

  mysqli_free_result($result);

  mysqli_close($con);
?>
<form id="spdx_form" action="spdx.php?doc_id=<?php echo $spdxId ?>" method="post">
<?php
echo "<input type=\"hidden\" name=\"doc_id\" value=\"$spdxId\" />";
echo "<input type=\"hidden\" name=\"action\" value=\"update\" />";
?>
<table id="tblMain" class="table table-bordered table-striped table-doc">
    <thead>
        <tr>
            <th colspan=2><?php if($hasRow)  echo $row["package_name"]; ?>
			    <button id="download_top" type="submit"  class="btn btn-primary" onclick="return setFormAction('spdx_form');">Download</button>
                <input id="edit_doc" type="button" class="btn btn-primary" value="Edit" onclick="hideCol(2);" />
				<button id="save_top" type="submit"  class="btn btn-primary">Save </button>
            </th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>Version</td>
        <td class="edit" style="display:none"><textarea name="spdx_version" class='form-control'><?php if($hasRow)  echo $row["spdx_version"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["spdx_version"]; ?></td>
    </tr>
    <tr>
        <td>Data License</td>
        <td class="edit" style="display:none"><textarea name="data_license" class='form-control'><?php if($hasRow)  echo $row["data_license"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["data_license"]; ?></td>
    </tr>
    <tr>
        <td>Document Comment</td>
        <td class="edit" style="display:none"><textarea name="document_comment"  class='form-control'><?php if($hasRow)  echo $row["document_comment"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["document_comment"]; ?></td>
    </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan=2>Creation Information</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>Creator</td>
        <td class="edit" style="display:none"><textarea name="creator" class='form-control'><?php if($hasRow)  echo $row["creator"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["creator"]; ?></td>
    </tr>
    <tr>
        <td>Created</td>
        <td><?php if($hasRow)  echo $row["created_at"]; ?></td>
    </tr>
    <tr>
        <td>Updated</td>
        <td><?php if($hasRow)  echo $row["updated_at"]; ?></td>
    </tr>
    <tr>
        <td>Creator Comment</td>
        <td class="edit" style="display:none"><textarea name="creator_comments" class='form-control'><?php if($hasRow)  echo $row["creator_comments"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["creator_comments"]; ?></td>
    </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan=2>Package Information</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>Package Name</td>
        <td class="edit" style="display:none"><textarea name="package_name" class='form-control'><?php if($hasRow)  echo $row["package_name"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_name"]; ?></td>
    </tr>
    <tr>
        <td>Package Version</td>
        <td class="edit" style="display:none"><textarea name="package_version" class='form-control'><?php if($hasRow)  echo $row["package_version"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_version"]; ?></td>
    </tr>
    <tr>
        <td>Package Download Location</td>
        <td class="edit" style="display:none"><textarea name="package_download_location" class='form-control'><?php if($hasRow)  echo $row["package_download_location"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_download_location"]; ?></td>
    </tr>
    <tr>
        <td>Package Summary</td>
        <td class="edit" style="display:none"><textarea name="package_summary" class='form-control'><?php if($hasRow)  echo $row["package_summary"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_summary"]; ?></td>
    </tr>
    <tr>
        <td>Package File Name</td>
        <td class="edit" style="display:none"><textarea name="package_file_name" class='form-control'><?php if($hasRow)  echo $row["package_file_name"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_file_name"]; ?></td>
    </tr>
    <tr>
        <td>Package Supplier</td>
        <td class="edit" style="display:none"><textarea name="package_supplier" class='form-control'><?php if($hasRow)  echo $row["package_supplier"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_supplier"]; ?></td>
    </tr>
    <tr>
        <td>Package Originator</td>
        <td class="edit" style="display:none"><textarea name="package_originator" class='form-control'><?php if($hasRow)  echo $row["package_originator"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_originator"]; ?></td>
    </tr>
    <tr>
        <td>Package Checksum</td>
        <td><?php if($hasRow)  echo $row["package_checksum"]; ?></td>
    </tr>
    <tr>
        <td>Package Verification Code</td>
        <td class="edit" style="display:none"><textarea name="package_verification_code" class='form-control'><?php if($hasRow)  echo $row["package_verification_code"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_verification_code"]; ?></td>
    </tr>
    <tr>
        <td>Package Description</td>
        <td class="edit" style="display:none"><textarea name="package_description" class='form-control'><?php if($hasRow)  echo $row["package_description"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_description"]; ?></td>
    </tr>
    <tr>
        <td>Package Copyright Text</td>
        <td class="edit" style="display:none"><textarea name="package_copyright_text" class='form-control'><?php if($hasRow)  echo $row["package_copyright_text"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_copyright_text"]; ?></td>
    </tr>
    <tr>
        <td>License Declared</td>
        <td><?php if($hasRow)  echo $row["package_license_declared"]; ?></td>
    </tr>
    <tr>
        <td>Package License Concluded</td>
		<td class="edit" style="display:none"><textarea name="package_license_concluded" class='form-control'><?php if($hasRow)  echo $row["package_license_concluded"]; ?></textarea></td>
        <td class="view"><?php if($hasRow)  echo $row["package_license_concluded"]; ?></td>
        
    </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan=2>File Information</th>
        </tr>
    </thead>
    <tbody>
	<?php
	
	$con=mysqli_connect("localhost","root","","spdx");
	// Check connection
	if (mysqli_connect_errno())
	  {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	  }


	$query = "SELECT DISTINCT " .
				  "package_files.*, doc_file_package_associations.package_id " .
				  "FROM " .
				  "package_files " .
				  "INNER JOIN " .
				  "doc_file_package_associations " .
				  "ON " .
				  "package_files.id = doc_file_package_associations.package_file_id ".
				  "where package_id = "  . $spdxId; 
				 

	$result = mysqli_query($con,$query);

	if($result != false){
        $recordNum=0;
		while($record = mysqli_fetch_assoc($result)) {
				$recordNum++;
				echo "<tr>
						<td colspan=\"2\">
						<a href=\"#\" class=\"plus-icon\" id=\"plus-icon-".$recordNum."\" onclick=\"showFileCol(".$recordNum."); return false;\" ><img src=\"img/plus-icon.png\" width=\"15\" height=\"15\"></a>
						<a href=\"#\" class=\"minus-icon\" id=\"minus-icon-".$recordNum."\" onclick=\"hideFileCol(".$recordNum."); return false;\" ><img src=\"img/minus-icon.png\" width=\"15\" height=\"15\"></a>
						<span id=\"span-file-name-".$recordNum."\" ondblclick=\"editFile('-file-name-',".$recordNum.");\">".$record["file_name"]."</span><input id=\"input-file-name-".$recordNum."\" type=\"text\" style=\"display:none\" value=\"".$record["file_name"]."\" />
						<button id=\"save-file-".$recordNum."\" type=\"button\" style=\"display:none\" class=\"btn btn-primary\" onclick=\"saveFile(".$recordNum.",".$record["id"].");\">Save</button>
						</td>
					</tr>
					<tr>
						<td class=\"file-info-col file-info-col-".$recordNum."\">License Concluded</td>
						<td class=\"file-info-col file-info-col-".$recordNum."\">
						 <span id=\"span-file-license-".$recordNum."\" ondblclick=\"editFile('-file-license-',".$recordNum.");\">".$record["license_concluded"]."</span><textarea class='form-control' id=\"input-file-license-".$recordNum."\" type=\"text\" style=\"display:none\">".$record["license_concluded"]."</textarea>
						</td>
					</tr>
					<tr>
						<td class=\"file-info-col file-info-col-".$recordNum."\">File Comment</td>
						<td class=\"file-info-col file-info-col-".$recordNum."\">
						<span id=\"span-file-comment-".$recordNum."\" ondblclick=\"editFile('-file-comment-',".$recordNum.");\">".$record["file_comment"]."</span><textarea class='form-control' id=\"input-file-comment-".$recordNum."\" type=\"text\" style=\"display:none\">".$record["file_comment"]."</textarea>		
						</td>
					</tr>";
		}
	}	
	 
?>
        
    </tbody>
</tbody>
</table>
<button id="download_top" type="submit"  class="btn btn-primary" onclick="return setFormAction('spdx_form');">Download</button>
<button id="save_bottom" type="submit"  class="btn btn-primary">Save </button>
</form>
<?php
  mysqli_free_result($result);

  mysqli_close($con);
?> 