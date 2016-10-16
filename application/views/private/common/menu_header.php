<div class="well">

        <div class="row">
                <div class="span6 pull-left">
                <h2>LibriVox Management Dashboard</h2>
                </div>


                <div class="pull-right">

                <a href="#" id="profile_modal_link" role="button" class="menu_link profile_modal_link" >My Profile</a>
                <a href="<?= base_url() ?>logout" class="menu_link">Logout</a>
                </div>
        </div>


        <div class="row">
                <div class="span6 pull-left">
                <ul class="nav nav-pills">  
                        <li class="dropdown all-camera-dropdown">  
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Projects<b class="caret"></b></a>  
                        <ul class="dropdown-menu">
                            <?php 
                              //check permissions 
                              $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
                              if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
                              {
                                 echo '<li data-filter-camera-type="all"><a href="'. base_url(). 'add_catalog_item/new">Add New Project</a></li>';
                              }
                            ?>   
                              
                            <li data-filter-camera-type="all"><a href="<?= base_url() ?>projects/<?= $user_id?>">My Projects</a></li> 

                            <?php 
                              //check permissions 
                              $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
                              if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
                              {
                                 echo '<li data-filter-camera-type="all"><a href="'. base_url(). 'add_catalog_item/search">Search Projects</a></li>';
                              }
                            ?>  

                            <li data-filter-camera-type="all"><a href="<?= base_url() ?>section_compiler">Section Compiler</a></li>

                            <?php 
                              //check permissions 
                              $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
                              if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
                              {
                                 echo '<li data-filter-camera-type="all"><a href="'. base_url(). 'validator">Validator</a></li>';
                              }
                            ?>  

                            
                        </ul> 
                        </li> 

                        <li class="dropdown all-camera-dropdown">  
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">People<b class="caret"></b></a>  
                        <ul class="dropdown-menu"> 

                            <?php 
                              //check permissions 
                              $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
                              if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
                              {
                                 echo '<li data-filter-camera-type="all"><a href="#" id="addnew_modal_link" class="profile_modal_link">Add New</a></li>';
                              }
                            ?>   
                              
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>volunteers/bc">Show BCs</a></li>  
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>volunteers/mc">Show MCs</a></li> 
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>volunteers/reader">Show Active Readers</a></li> 
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>volunteers/all">Show All Users</a></li>
                        </ul> 
                        </li>  

                        <li class="dropdown all-camera-dropdown">  
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Stats<b class="caret"></b></a>  
                        <ul class="dropdown-menu">  
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>stats/sections" >My Sections</a></li>

                            <?php 
                              //check permissions 
                              $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
                              if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id']))
                              {
                                 echo '<li data-filter-camera-type="all"><a href="'. base_url(). 'stats/mc_stats" >MC Stats</a></li>';
                                 echo '<li data-filter-camera-type="all"><a href="'. base_url(). 'stats/active_projects" >Active Projects</a></li>';
                              }
                            ?>

                              
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>stats/monthly_stats" >Monthly Stats</a></li>
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>stats/general_stats" >General Stats</a></li>
                            <li data-filter-camera-type="all"><a href="<?= base_url()?>stats/chapters_count" >Chapters Count</a></li>
                            
                        </ul> 
                        </li> 

                        <li class="dropdown all-camera-dropdown">  
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Links<b class="caret"></b></a>  
                        <ul class="dropdown-menu">  
                            <li data-filter-camera-type="all"><a href="http://archive.org/details/librivoxaudio">Internet Archive</a></li> 
                            <li data-filter-camera-type="all"><a href="http://www.gutenberg.org/">Project Gutenberg</a></li> 
                            <li data-filter-camera-type="all"><a href="http://librivox.org">Librivox Main site</a></li> 
                            <li data-filter-camera-type="all"><a href="<?= base_url() ?>search">Librivox Catalog Search</a></li> 
                            <li data-filter-camera-type="all"><a href="http://wiki.librivox.org/index.php">Librivox Wiki</a></li> 
                            <li data-filter-camera-type="all"><a href="<?= base_url() ?>workflow">Librivox Manage Dashboard</a></li>  
                            <li data-filter-camera-type="all"><a href="<?= base_url() ?>uploader">Librivox File Uploader</a></li> 
                            <li data-filter-camera-type="all"><a href="https://forum.librivox.org">Librivox Forum</a></li>  
                        </ul> 
                        </li>

                        <?php 
                          //check permissions 
                          $allowed_groups = array(PERMISSIONS_ADMIN, PERMISSIONS_MCS);
                          if ($this->librivox_auth->has_permission($allowed_groups, $this->data['user_id'])): ?>
                          
                          <li class="dropdown all-camera-dropdown"> 
                          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin<b class="caret"></b></a>  
                          <ul class="dropdown-menu">                            
                              <li><a href="<?= base_url()?>admin/author_manager" >Edit Authors</a></li> 
                              <li><a href="<?= base_url()?>admin/genre_manager" >Edit Genres</a></li> 
                              <li><a href="<?= base_url()?>admin/language_manager" >Edit Language</a></li>
                              <li><a href="<?= base_url()?>private/groups" >Manage Groups</a></li>
                              
                          </ul>
                          </li>

                        <?php endif; ?> 


                        <li class="dropdown all-camera-dropdown">  
                            <a href="<?= base_url()?>pages/workflow-help" >Help</a> 
                        </li>                         
                </ul> 
                </div>


                <div class="pull-right">
                     <form id="project_search_form" method="post" action="<?= base_url()?>projects">   
                     <div class="control-group">
                         <div class="controls pull-right">
                                <label for="">Search by Project ID, Title, Author
                                <input type="text" class="no-margin" name="project_search" id="project_search" value="<?= empty($project_search)? '': $project_search; ?>">
                                <button class="btn btn-small btn-primary">Go</button>
                                </label>
                         </div>
                     </div>
                     </form> 
                     
                     <form id="user_search_form" method="post" action="<?= base_url()?>volunteers">
                     <div class="control-group">
                         <div class="controls pull-right">
                                <label for="">Search by reader/user
                                <input type="text" class="no-margin" name="user_search" id="user_search" value="<?= empty($search_term)? '': $search_term; ?>">
                                <button class="btn btn-small btn-primary">Go</button>
                                </label>
                         </div>
                    </div> 
                    </form>                         
                     
                </div>
        </div>

</div>

<!-- Modal for profile here -->

<?= (empty($profile_modal))? '':$profile_modal; ?>