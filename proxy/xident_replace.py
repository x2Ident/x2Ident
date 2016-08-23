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
    
    # write real ip to header
    # flow.request.headers["newheader"] = "foo"
    client_ip_wport = str(flow.client_conn.address)
    client_ip = client_ip_wport.split(":")[0]
    #print(client_ip)
    flow.request.headers["xident-real-ip"] = str(client_ip)
    try:
        user_agent = flow.request.headers["User-Agent"]
    except:
        user_agent = "none"
    #print(user_agent)

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
        if "noscio.eu/xIdent" not in flow.request.url:
            if "mitm.it" not in flow.request.url:
                resp = HTTPResponse(
                    b"HTTP/1.1", 303, b"See Other \nLocation: https://noscio.eu/xIdent",
                    Headers(Location="https://noscio.eu/xIdent"),
                    b"<html><head><title>Access Denied</title></head><body><h1>Unberechtigter Zugriff</h1> <a href=\"https://noscio.eu/xIdent\">Login: https://noscio.eu/xIdent</a></body></html>"
                )
                flow.reply.send(resp)
                print("redirect to xIdent landing page")

    if "noscio.eu/xi-proxy" in flow.request.url:
        print("replaced xi-proxy url")
        flow.request.url = flow.request.url.replace("noscio.eu/xi-proxy/","")
        print(flow.request.url)
        request_host = flow.request.url.split("://")[1].split("/")[0]
        flow.request.host = request_host
        print("host: "+request_host)
    else:
        redirect_url = "https://noscio.eu/xi-proxy/"+flow.request.url.split("://")[1]
        if "mitm.it" not in flow.request.url:
                print("redirect to url: "+redirect_url)
                resp = HTTPResponse(
                    b"HTTP/1.1", 303, b"See Other \nLocation: "+redirect_url,
                    Headers(Location=redirect_url),
                    b"<html><head><title>Redirect</title></head><body><h1>Redirect</h1> <a href=\"https://noscio.eu/xIdent\">Login: https://noscio.eu/xIdent</a></body></html>"
                )
                flow.reply.send(resp)
                print("redirect to xi-proxy domain")

    # query = "INSERT INTO onetimekeys (pwid, onetime, real_pw, pw_active) VALUES ('100', 'asdfjkö', 'imagine','1')"
    # cur.execute(query)
    cur.execute("SELECT pwid, onetime, real_pw, expires FROM `onetimekeys` ")
    replaced = False
    pwid = ""
    for row in cur.fetchall():
        pwid = str(row[0])
        expires = row[3]
        if expires<time.time():
            query = "UPDATE onetimekeys SET pw_active=0 WHERE pwid="+str(pwid)
            cur.execute(query)
            # print("deleted item because it expired (timestamp:"+str(time.time())+", expire:"+str(expires));
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
