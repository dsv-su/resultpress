<div class="page-content page-container" id="page-content">
    <div class="padding">
        <div class="row container d-flex justify-content-center">
            <div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalLabel">Invite a partner to this project <strong>{{$project->name}}</strong></h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </div>
                        <div class="modal-body">
                            <form id="inviteform" method="post" action="{{route('process_invite')}}">
                                @csrf
                                <div class="form-group"> <label for="recipient-name" class="col-form-label">Recipient:</label> <input type="email" class="form-control" id="recipient-name" name="email"> </div>
                                <input type="number" name="project_id" value="{{$project->id}}" hidden>
                                <div class="modal-footer"> <button type="submit" class="btn btn-success" >Send invite</button> <button type="button" class="btn btn-light" data-dismiss="modal">Close</button> </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#inviteform').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: '{{route('process_invite')}}', //this is the submit URL
                type: 'POST', //or POST
                data: $('#inviteform').serialize(),
                success: function(data){
                    $('#inviteModal').modal('toggle');
                    window.location.reload(true);
                }
            });
        });
    });
</script>
