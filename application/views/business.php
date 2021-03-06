<section>
 <div class="header-inner two">
   <div class="inner text-center">
    <h4 class="title text-white uppercase">BROWSE BY SECTOR</h4>
   </div>
   <div class="overlay bg-opacity-5"></div>
   <img src="<?php echo asset_url();?>images/header-img.jpg" alt="" class="img-responsive" />
 </div>
</section>
<!-- end header inner -->
<div class="clearfix"></div>

<section class="sec-padding paddbott">
 <div class="container">
  <div class="row">
   <div class="col-sm-9">
    <div class="col-md-12 filter-box padd0">
     <div class="col-md-5">
      <div class="form-group">
       <label for="inputEmail3" class="col-sm-3 control-label">Sort by:</label>
       <div class="col-sm-9">
	<select class="form-control" id="sortby" name="sortby" onChange="changeSorting(this.value)">
<?php
         foreach( unserialize(ITEM_SORT_BY_OPTIONS) as $sortValue => $sortName ){
?>
	<option value="<?php echo $sortValue;?>" <?php if( $this->input->get('sortby') == $sortValue ){echo 'selected="selected"';}?>><?php echo $sortName;?></option>
<?php
	 }
?>
        </select>
       </div>
      </div>
     </div>
				
     <div class="col-md-5">
      <div class="form-group">
       <label for="inputEmail3" class="col-sm-6 control-label">Results per page:</label>
       <div class="col-sm-6">
        <select class="form-control" id="ipp" name="ipp" onChange="changePageSize(this.value)">
<?php
         foreach( unserialize(ITEM_PER_PAGE_COUNT) as $itemCountValue => $itemCountDisplay ){
?>
	<option value="<?php echo $itemCountValue?>" <?php if( $this->input->get('ipp') == $itemCountValue ){echo 'selected="selected"';} ?>><?php echo $itemCountDisplay?></option>
<?php
	 }
?>
        </select>
       </div>
      </div>
     </div>
    </div>

<?php
	$hostname="localhost";
$username="root";
$password="";
$dbname="managbyc_bdeals";

 
//Connect to the database
$connection = mysql_connect($hostname, $username, $password);
mysql_select_db($dbname, $connection);
 
 //featured listing
  $fetListing2="select * from  bd_buy_business_details t1
left join bd_sell_business_details t2 on t1.form_id=t2.form_id 
left join bd_business_details t3 on t1.form_id=t3.form_id 
left join bd_country_list t4 on t3.location_country=t4.id
left join bd_country_state_mapping t5 on t3.location_state=t5.id
left join bd_state_city_mapping t6 on t3.location_city=t6.id";
 $query2=mysql_query($fetListing2) or die(mysql_error());
 while($searchData=mysql_fetch_assoc($query2)){
?>
<form name="lb_uprof<?php echo $i; ?>" method="post" action="../browse-sector-listing-details" method="post" data-toggle="validator" role="form" class="was-validated">
<input type="hidden" name="frm_id" id="frm_id" value="<?php echo $searchData['form_id'];?>" />
<input type="hidden" name="business_type" id="business_type" value="<?php echo $searchData['business_type'];?>" />

    <div class="item-listing bg-grey">
     <div class="col-md-4">
     <img class="img-responsive" src="<?php echo asset_url();?>img/s1.jpeg" alt="<?php echo $searchData['key_headline'];?>">
     </div>
     <div class="col-md-8 listing-title">							
      <h3><?php echo $searchData['key_headline'];?></h3> 
      <p> <span><b>Asking Price:</b> <?php echo $searchData['ask_price'];?> <?php echo $searchData['asking_price'];?> </span> <span><b>Sales Revenue:</b> <?php echo $searchData['latest_revenu_currency'];?> <?php echo $searchData['latest_revenu'];?></span></p>

      <p><?php echo $searchData['country_name']?>,
	  <?php echo $searchData['state_name'];?>, 
	  <?php echo $searchData['city_name'];?></p>
      <p><?php if( strlen( $searchData['description'] ) > 195 ){echo substr($searchData['description'], 0, 195).' ....';}else{echo $searchData['description'];} ?></p>
      <div class="listing-enquiry-sec">
       <ul>
	  	<p>  <div class="pull-right "><span> <a class="view-text" href="<?php echo base_url().'/index.php/home/viewall'?>?id=<?php echo $searchData["form_id"];?>&list=Mandate">View more</a></span> </div></p>
						
        <!--<li><a href="../browse-sector-listing-details?form_id=<?php echo $searchData['form_id'];?>"><i class="fa fa-star-o" aria-hidden="true"></i> View More</a> --></li>
       </ul>
      </div>

     </div>
    </div>
</form>
<?php
   }
 /*
?>

    <div class="item-listing bg-grey">
     <div class="col-md-4">
     <img class="img-responsive" src="<?php echo asset_url();?>img/s1.jpeg" alt="">
     </div>
     <div class="col-md-8 listing-title">							
      <h3>Running pesticides unit for sale</h3> 
      <p> <span><b>Asking Price:</b> Rs. 2.75 Crs</span> <span><b>Sales Revenue:</b> Rs. Na</span></p>
      <p>India, Madhya Pradesh, Indore</p>
      <p>Running unit of Pesticides located on the industrial area and located near the city's industrial capital, Indore in Madhya Pradesh, in which a set of 15,000 square feet above 10000 sq ft of land,  ....</p>
      <div class="listing-enquiry-sec">
       <ul>
        <li><a href="#!"><i class="fa fa-star-o" aria-hidden="true"></i> View More</a> </li>
       </ul>
      </div>
     </div>
    </div>
<?php
  */
?>

    <div class="col-md-12 mt40">        
     <?php echo $links;?>
<?php /*<ul class="blog-pagenation">
      <li><a href="#"><i class="fa fa-angle-left"></i></a></li>
      <li><a href="#">1</a></li>
      <li><a class="active" href="#">2</a></li>
      <li><a href="#">3</a></li>
      <li><a href="#">4</a></li>
      <li><a href="#">5</a></li>
      <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
      </ul> */ ?>
    </div>	
   </div>
   <div class="col-sm-3">
    <div class="feat-c-box red-ad">
    <img src="<?php echo asset_url();?>img/add2.jpg" class="img-responsive">
    </div>
   </div>
  </div>
 </div>
</section>
  <!--end section-->
<script>
	function changePageSize( itemPerPage ){
		var oldUrl = window.location.href;
		var newUrl = '';
		if( oldUrl.indexOf( 'ipp' ) != -1 ){
			oldUrl = oldUrl.substring( 0, oldUrl.indexOf('&ipp=')+5);
			oldUrl = oldUrl.substring( 0, oldUrl.lastIndexOf("/") ) +'/?'+ oldUrl.substring( oldUrl.indexOf("?")+1, oldUrl.length );
			newUrl = oldUrl+itemPerPage;
		}else{
			oldUrl = oldUrl.substring( 0, oldUrl.lastIndexOf("/") ) +'/?'+ oldUrl.substring( oldUrl.indexOf("?")+1, oldUrl.length );
			newUrl = oldUrl+'&ipp='+itemPerPage;
		}
		window.location.href = newUrl;
	}

	function changeSorting( sortingOption ){
		var oldUrl = window.location.href;
		var newUrl = '';
		if( oldUrl.indexOf( 'sortby' ) != -1 ){
			oldUrl = oldUrl.substring( 0, oldUrl.indexOf('&sortby=')+8);
			oldUrl = oldUrl.substring( 0, oldUrl.lastIndexOf("/") ) +'/?'+ oldUrl.substring( oldUrl.indexOf("?")+1, oldUrl.length );
			newUrl = oldUrl+sortingOption;
		}else{
			oldUrl = oldUrl.substring( 0, oldUrl.lastIndexOf("/") ) +'/?'+ oldUrl.substring( oldUrl.indexOf("?")+1, oldUrl.length );
			newUrl = oldUrl+'&sortby='+sortingOption;
		}
		window.location.href = newUrl;
	}


</script>
