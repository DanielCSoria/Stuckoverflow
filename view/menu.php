 <nav class="navbar mb-5 fixed-top navbar-light  navbar-expand-md bg-faded">
     <a class="navbar-brand d-flex w-50 mr-auto"> Stuck Overflow</a>
     <ul id="navig" class="nav navbar-nav ml-auto w-100 justify-content-end">
         <li class="nav-item">
             <a class="nav-link mr-2" href="post/index">Questions</a>
         </li>
         <li class="nav-item">
             <a class="nav-link mr-2" href="tag">Tags</a>
         </li>
         <li class="nav-item" id="stats">
             
         </li>
         <li id="userSpec" class="d-flex">
             <?php if ($user != false) : ?>
         <li class="nav-item">
             <a class="nav-link mr-2" href="post/ask">Ask a question</a>
         </li>
         <li class="nav-item ">
             <a class="nav-link disabled"><i class="fas fa-user menu"></i></a>
         </li>
         <li class="nav-item ">
             <a class="nav-link mr-2 disabled"><span class="menu px-0 mx-0"><?= $user->get_name() ?></span></a>
         </li>
         <li class="nav-item ">
             <a class="nav-link mr-2" href="user/logout_confirm" id="signoutBtn"><i class="fas fa-sign-out-alt menu"></i></a>
         </li>
     <?php else : ?>
         <li class="nav-item">
             <a class="nav-link mr-2 " href="user/signup" id="signupBtn"><i class="fas fa-user-plus menu"></i></a>
         </li>
         <li class="nav-item">
             <a class="nav-link mr-2 " href="user/login" id="signinBtn"><i class="fas fa-sign-in-alt menu"></i></a>
         </li>
     <?php
                endif; ?>
     </ul>

 </nav>