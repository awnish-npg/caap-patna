<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel-header">
                            <h1>Region Filter</h1>
                            <hr class="hr-panel-heading" />
                        </div>
                    </div>
                    <select name="skills" id="sub_region_id" multiple="" class="ui fluid dropdown">
                        <option value="">Skills</option>
                        <option value="angular">Angular</option>
                        <option value="css">CSS</option>
                        <option value="design">Graphic Design</option>
                        <option value="ember">Ember</option>
                        <option value="html">HTML</option>
                        <option value="ia">Information Architecture</option>
                        <option value="javascript">Javascript</option>
                        <option value="mech">Mechanical Engineering</option>
                        <option value="meteor">Meteor</option>
                        <option value="node">NodeJS</option>
                        <option value="plumbing">Plumbing</option>
                        <option value="python">Python</option>
                        <option value="rails">Rails</option>
                        <option value="react">React</option>
                        <option value="repair">Kitchen Repair</option>
                        <option value="ruby">Ruby</option>
                        <option value="ui">UI Design</option>
                        <option value="ux">User Experience</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(e){
    $('#sub_region_id')
			.dropdown({
				placeholder: "Select Sub-Region*",
				transition: "slide down",
			});
})
</script>
<?php init_tail(); ?>