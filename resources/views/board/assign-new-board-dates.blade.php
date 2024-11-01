
<div class="add-member">
    <button class="close-btn-pop close-member">
        <span class="material-symbols-outlined">close</span>
    </button>
    <h4 class="mem-heading">Dates</h4>
    <!-- date calendar -->
    <div class="datepicker"></div>
    <h4 class="due-heading">Start date</h4>
    <div class="modal-start-date">
        <input type="checkbox" id="check-start-date"/>
        <div class="input-date-group">
            <input type="text" id="board-start-date"
                   placeholder="M/D/YYYY" data-testid="due-date-field"
                   aria-placeholder="M/D/YYYY"
                   {{--value="{{\Carbon\Carbon::now()->addDay()->format('m/d/Y')}}"--}} disabled/>
        </div>
    </div>
    <h4 class="due-heading">Due date</h4>
    <div class="modal-due-date">
        <input type="checkbox" id="check-due-date"/>
        <div class="input-date-group">
            <input type="text" id="board-due-date"
                   placeholder="M/D/YYYY" data-testid="due-date-field"
                   aria-placeholder="M/D/YYYY"
                   {{--value="{{\Carbon\Carbon::now()->addDays(2)->format('m/d/Y')}}"--}} disabled/>
            <input type="text" id="board-due-time" placeholder="h:m A"
                   aria-placeholder="h:m A"
                   {{--value="{{ \Carbon\Carbon::now()->addDays(2)->format('h:m A') }}"--}} disabled/>
        </div>
    </div>
    <button class="show-btn date-cal-btn"
            id="board-dates-save-btn">Save
    </button>
    <button class="show-btn" id="board-dates-remove-btn">Remove</button>
</div>
