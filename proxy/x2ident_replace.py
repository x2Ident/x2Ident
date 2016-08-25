# (this script works best with --anticache)
import sys
import time
import MySQLdb
from mitmproxy.models import HTTPResponse
from netlib.http import Headers

def request(flow):

    db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="xident",         # your username
                     passwd="jugendhackt",  # your password
                     db="xident")        # name of the data base
    cur = db.cursor()
    
    # delete user session if expired
    timestamp = time.time()
	# TODO: also delete OTKs related to session
    query = "DELETE FROM session_user WHERE expires<"+str(timestamp)
    cur.execute(query)

    # write real ip to header
    client_ip_wport = str(flow.client_conn.address)
    client_ip = client_ip_wport.split(":")[0]
    #print(client_ip)
    flow.request.headers["xident-real-ip"] = str(client_ip)
    try:
        user_agent = flow.request.headers["User-Agent"]
    except:
        user_agent = "none"

	# check if user is on a xident page
    if "noscio.eu/xIdent" in flow.request.url:
        return flow

    # herausfinden, ob Client zur Nutzung berechtigt ist
    berechtigt = False;
    cur.execute("SELECT user, ip, user_agent FROM `session_user` WHERE ip='"+str(client_ip)+"'")
    for row in cur.fetchall():
        if row[2] in user_agent:
            berechtigt = True
            print("berechtigt")
    if berechtigt==False:
        print(client_ip+": nicht berechtigt")
        if "noscio.eu/x2ident_raw" not in flow.request.url:
            if "mitm.it" not in flow.request.url:
                # answer with a redirect to the landing page
                resp = HTTPResponse(
                    b"HTTP/1.1", 303, b"See Other \nLocation: https://noscio.eu/x2ident",
                    Headers(Location="https://noscio.eu/x2ident_raw"),
                    b"<html><head><title>Access Denied</title></head><body><h1>Unberechtigter Zugriff</h1> <a href=\"https://noscio.eu/x2Ident_raw\">Login: https://noscio.eu/x2dent_raw</a></body></html>"
                )
                flow.reply.send(resp)
                print("redirect to xIdent landing page")

    
    cur.execute("SELECT pwid, onetime, real_pw, expires, url, pw_global FROM `onetimekeys` ")
    replaced = False
    pwid = ""
    for row in cur.fetchall():
        pwid = str(row[0])
        expires = row[3]
        url = row[4]
        url_valide = False
        url_pattern = ""
        pw_global = int(float(row[5]))
        if(len(url)==0):
            url_valide = True
        if(pw_global==1):
            url_valide = True
        try:
            url_pattern = "://"+url.split("://")[1] # ignore protocol, but not subdomains!
        except:
            print("failed generate url_pattern: "+url)
            url_valide = True
        if url_pattern in flow.request.url:
            url_valide = True
        if url_valide:            
            if expires<time.time():
                query = "UPDATE onetimekeys SET pw_active=0 WHERE pwid="+str(pwid)
                cur.execute(query)
                print("deleted item because it expired (timestamp:"+str(time.time())+", expire:"+str(expires))
            else:
                if row[1] in flow.request.content:
                    pwid = str(row[0])
                    print("replaced "+str(row[1])+" with "+str(row[2]))
                    flow.request.content = flow.request.content.replace(
                        str(row[1]),
	                    str(row[2])
                    )
                    timestamp = str(time.time())
                    query = "UPDATE onetimekeys SET pw_active=0, expires=0 WHERE pwid="+str(pwid)
                    cur.execute(query)
                    print("deleted item because it was used");
                    query = "DELETE FROM history WHERE pwid="+pwid
                    cur.execute(query)
                    query = "INSERT INTO history (pwid, last_login) VALUES ("+pwid+","+timestamp+")"
                    cur.execute(query)            
    db.commit()
    cur.close()
