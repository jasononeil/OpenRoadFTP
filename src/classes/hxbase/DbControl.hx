package hxbase;

class DbControl
{
	public static var cnx:php.db.Connection = null;
	
	public static function connect()
	{
		if (cnx == null)
		{
			cnx = php.db.Mysql.connect(
			{
				host : AppConfig.dbServer,
				port : AppConfig.dbPort,
				database : AppConfig.dbDatabase,
				user : AppConfig.dbUsername,
				pass : AppConfig.dbPassword,
				socket : null
			});
			
			php.db.Manager.cnx = cnx;
			php.db.Manager.initialize();
		}
		// else it's already connected!
		
		/*
		Making sure we don't double connect, we should save time.
		Here's some very raw tests on my laptop:	
			// 0 connects: 0.0021381378173828
			// 1 connects: 0.025954008102417
			// 10 connects: 0.028225898742676
			// 100 connects: 0.029486894607544
			// 1000 connects: 0.030943155288696
		*/
	}
	
	public static function close()
	{
		// close the connection and do some cleanup
		php.db.Manager.cleanup();
		cnx.close();
		cnx = null;
	}

}
