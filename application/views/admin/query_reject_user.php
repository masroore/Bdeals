<?php
 //var_dump($employees);
 //var_dump($userDetails);
$user_id=$this->uri->segment('4');
?>
<div class="main-content">
<h1 class="page-title"><?php echo $heading;?> (<?php echo $queryDetails[0]['name']?>)</h1>
 <div class="row ">
  <div class="col-md-12">
   <div class="panel panel-default">
    <div class="panel-body">
    <form class="form-horizontal" method="POST" action="<?php echo base_url();?>admin/query/reject_user/<?php echo $user_id;?>">
      <input type="hidden" id="status" name="status" value="Reject" />
      <input type="hidden" id="to" name="to" value="<?php echo $to;?>" />
<?php /*
      <div class="form-group"> 
       <label class="col-sm-2 control-label" for="status">Status</label> 
       <div class="col-sm-5"> 
        <select class="form-control" id="status" name="status">
         <option value="">Select Status</option>
<?php
foreach( unserialize(ASSIGN_STATUS) as $assignedStatusValue => $assignedStatusName ){
?>
	<option value="<?php echo $assignedStatusValue;?>"><?php echo $assignedStatusName;?></option>
<?php
}
?>
	</select>
       </div> 
      </div>
 */ ?>
      <div class="form-group"> 
       <label class="col-sm-2 control-label" for="inputEmail3">Reason</label> 
       <div class="col-sm-5"> 
        <textarea placeholder="Reason" rows="3" class="form-control" id="comment" name="comment"></textarea>
        <input type="hidden" id="query_id" name="query_id" value="<?php echo $query_id;?>" />
       </div>
      </div>
      <div class="form-group"> 
       <div class="col-sm-offset-2 col-sm-10"> 
        <button class="btn btn-primary" type="submit">Reject</button> 
       </div>
      </div>
     </form>
    </div>
   </div>
  </div>
</div>
