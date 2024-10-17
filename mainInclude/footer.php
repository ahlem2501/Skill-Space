
 

    <!-- Start Student Registration Modal -->
    <div class="modal fade" id="stuRegModalCenter" tabindex="-1" role="dialog" aria-labelledby="stuRegModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="stuRegModalCenterTitle">Student Registration</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearAllStuReg()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!--Start Form Registration-->
            <?php include('studentRegistration.php'); ?>
            <!-- End Form Registration -->
          </div>
          <div class="modal-footer">
            <span id="successMsg"></span>
            <button type="button" class="btn btn-primary" id="signup" onclick="addStu()">Sign Up</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="clearAllStuReg()">Close</button>
          </div>
        </div>
      </div>
    </div> <!-- End Student Registration Modal -->


    <!-- Start Student Login Modal -->
    <div class="modal fade" id="stuLoginModalCenter" tabindex="-1" role="dialog" aria-labelledby="stuLoginModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="stuLoginModalCenterTitle">Student Login</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="clearStuLoginWithStatus()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form role="form" id="stuLoginForm">
              <div class="form-group">
                <i class="fas fa-envelope"></i><label for="stuLogEmail" class="pl-2 font-weight-bold">Email</label><small id="statusLogMsg1"></small><input type="email"
                    class="form-control" placeholder="Email" name="stuLogEmail" id="stuLogEmail">
                </div>
                <div class="form-group">
                  <i class="fas fa-key"></i><label for="stuLogPass" class="pl-2 font-weight-bold">Password</label><input type="password" class="form-control" placeholder="Password" name="stuLogPass" id="stuLogPass">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <small id="statusLogMsg"></small>
            <button type="button" class="btn btn-primary" id="stuLoginBtn" onclick="checkStuLogin()">Login</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="clearStuLoginWithStatus()">Cancel</button>
          </div>
        </div>
      </div>
    </div> <!-- End Student Login Modal -->


  

    <!-- Jquery and Boostrap JavaScript -->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <!-- Font Awesome JS -->
    <script type="text/javascript" src="js/all.min.js"></script>

    <!-- Student Testimonial Owl Slider JS  -->
    <script type="text/javascript" src="js/owl.min.js"></script>
    <script type="text/javascript" src="js/testyslider.js"></script>

    <!-- Student Ajax Call JavaScript -->
    <script type="text/javascript" src="js/ajaxrequest.js"></script>

    

   

  </body>

</html>