<div class="dropdown-menu add-member-drop labeldropdown-inner right-side-label-tab">
    <div class="add-member">
        <button class="close-btn-pop close-member" onClick="closelabelDiv()">
            <span class="material-symbols-outlined">close</span>
        </button>
        <h4 class="mem-heading">Label</h4>
        <form class="card-label">
            <div class="form-group">
                <input type="text" class="form-control"
                       id="searchMember"
                       aria-describedby="searchHelp"
                       placeholder="Search Labels">
            </div>

            <div class="labels-data"></div>
                <div class="show-more-label d-none">
                </div>
        </form>
        <button class="show-btn dynamiclabelBtn" onClick="openlabelDiv()"
                data-target="popover-new" id="new-label-btn">Create a new
            label
        </button>
        <button class="show-btn show-btn-label showing-toggle">Show more label</button>
        <button class="show-btn">Enable Colorblind friendly mode
        </button>
    </div>
</div>
