<?php
include '../include/conexao/conecta.php';
 
  $id = $_GET['id_usuario'];
 
  $deleta = mysqli_query($conexao,"DELETE FROM usuario WHERE id_usuario = '$id'");
 
  if($deleta == ''){
   echo "<script>alert('Houve um erro ao deletar!');
   location.href=\"index.php\"</script>";
}else{
   echo "<script>alert('Registro excluido com sucesso!');
   location.href=\"index.php\"</script>";
}
 
?>                                                                                                                                                                                                                                                                                                                                                                                                                            
