  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
   <div class="modal-content">
        <div class="modal-header" style="padding:35px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4><span class="glyphicon glyphicon-lock"></span> NEW people</h4>
        </div>
        <div class="modal-body" style="padding:40px 50px;">
          <form action='newpeople' method='post' id='frmpeople'>
            <div class="form-group">
              <label for="lname"><span class="glyphicon glyphicon-user"></span> lname</label>
              <input type="text" class="form-control" id="lname" placeholder="Enter lname" name='lname'>
            </div>         
            <div class="form-group">
              <label for="fname"><span class="glyphicon glyphicon-user"></span> fname</label>
              <input type="text" class="form-control" id="fname" placeholder="Enter fname" name='fname'>
            </div> 
            <div class="form-group">
             
                <select name='sex' id='sex'>
                  <option>Select Sex</option>
                  <option value='0'>Male</option>
                  <option value='1'>Female</option>
                </select>

            </div>    
            <div class="form-group">
              <label for="email"><span class="glyphicon glyphicon-envelope"></span> email</label>
              <input type="text" class="form-control" id="email" placeholder="Enter email" name='email'>
            </div>
              <div class="form-group">
              <label for="phone"><span class="glyphicon glyphicon-phone"></span> Phone</label>
              <input type="text" class="form-control" id="phone" placeholder="Enter phone" name='phone'>
            </div>    
            <div class="form-group">
              <label for="password"><span class="glyphicon glyphicon-pas"></span> password</label>
              <input type="text" class="form-control" id="password" placeholder="Enter password" name='password'>
            </div>
           
            <div class="checkbox">
            </div>
              <button type="submit" id='save' value='Save' class="btn btn-primary"><span class="glyphicon glyphicon-book"></span> Save</button>
          </form>
        </div>
        <input type='hidden' name='id' id='id' value=''>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
       
       
        </div>
      </div>
</div> 
</div>