package main

import (
	"net/http"
	"os"
)

func main() {
	host := "https://releases.jinya.de/cms/push/"+os.Getenv("TAG_NAME")
	if contains(os.Args, "-debug") {
		host = "http://localhost:8090/cms/push/"+os.Getenv("TAG_NAME")
	}
	if contains(os.Args, "-unstable") {
	    host = "https://releases.jinya.de/cms/unstable/push/24.1."+os.Getenv("BUILD_NUMBER")+"-unstable"
    }
	file, err := os.Open("jinya-cms.zip")
	if err != nil {
		panic(err)
	}
	req, err := http.NewRequest(http.MethodPost, host, file)
	if err != nil {
		panic(err)
	}

	req.Header.Set("JinyaAuthKey", os.Getenv("JINYA_RELEASES_AUTH"))
	_, err = http.DefaultClient.Do(req)

	if err != nil {
		panic(err)
	}
}

func contains(s []string, e string) bool {
	for _, a := range s {
		if a == e {
			return true
		}
	}
	return false
}