<div class="modal fade bd-example-modal-lg" id="mailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ url('sendEmailToUser') }}" enctype="multipart/form-data" autocomplete="off">

                @csrf
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label>To:</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" id="emailsend_to" name="emailsend_to" readonly class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label>Subject:</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" id="emailsend_subject" name="emailsend_subject" required class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label>Message:</label>
                        </div>
                        <div class="col-md-10">
                            <textarea class="form-control" id="emailsend_message" name="emailsend_message" required rows="7"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
<footer class="footer">
    @include('layouts.footers.nav')
</footer>
<!-- Modal -->