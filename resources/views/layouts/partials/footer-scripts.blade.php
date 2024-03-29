<div id="footer">
    <div id="footer-name">
        <div id="footer-dsv">
            Department of Computer and Systems Sciences
        </div>
        <div id="footer-su">
            Stockholm University
        </div>
    </div>
    <div id="footer-contact">
        <a id="footer-contact-link" href="http://dsv.su.se/en/about/contact" accesskey="7">Contact</a>
    </div>
    <div class="clear">
    </div>
</div>
<script>
    $(function(){
        $('input.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            weekStart: 1
        });
    });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();

        $('.project-permission').toggle();
        $('.projects-permissions').click(function(e) {
            e.preventDefault();
            $('.project-permission').toggle();
            return false;
        });
    })
</script>
