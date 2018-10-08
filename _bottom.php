</div> <!-- /.container -->


            <!-- FOOTER  -->
            <footer class="container">
                <!--<p class="float-right"><a href="#">Back to top</a></p> -->
                <!-- <p>dev by Daniel Lima <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p> -->
            </footer>
        </main>

        
        <script src="<?php echo HOME; ?>/_js/bootstrap.min.js" crossorigin="anonymous"></script>
        
        <script type="text/javascript">
            $(document).ready(function(){ 
                $(".f-money").inputmask('decimal', {
                    'alias': 'numeric',
                    'groupSeparator': '',
                    'autoGroup': true,
                    'digits': 2,
                    'radixPoint': ".",
                    'digitsOptional': false,
                    'allowMinus': false,
                });
                
                $('.fone').inputmask('(99) 99999-9999');
            });

        </script>
        
        
    </body>
</html>