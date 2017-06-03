package main

import (
    "log"
    "os"
    "os/exec"
    "fmt"
    "io/ioutil"
    "encoding/json"

    "github.com/fsnotify/fsnotify"
)

var (
    settings struct {
        ServerPath string `json:"server_path"`
        Port string `json:"port"`
        Host string `json:"host"`
        DirectoriesToWatch []string `json:"directories_to_watch"`
    }
    ProcessID int = 0
)

func clearScreen() {
    c := exec.Command("clear")
    c.Stdout = os.Stdout
    c.Run()
}

func startApp(pathToServer string, port string, host string) {
    if port != "" {
        port = "--port=" + port
    }

    if host != "" {
        host = "--host=" + host
    }

    systemWithoutOutput("php", pathToServer, port, host)
}

func stopApp() {
    killCommand := fmt.Sprintf("kill %d", ProcessID)
    systemWithoutOutput("sh","-c", killCommand)
}

func restartApp(serverPath string, port string, host string) {
    clearScreen()
    fmt.Println("Restarting app")
    stopApp()
    startApp(serverPath, port, host)
}

func systemWithoutOutput(cmd string, arg ...string) {
    command := exec.Command(cmd, arg...)
    command.Stdout = os.Stdout
    err := command.Start()

    if err != nil {
        log.Fatal(err)
    }

    ProcessID = command.Process.Pid
}

func getDevServerConfig () {
    jsonFile, err := ioutil.ReadFile("./reactor.config.json")
    if err != nil {
        log.Fatal(err)
    }

    json.Unmarshal(jsonFile, &settings)
}

func initConsole (directoriesBeingWatch []string) {
    fmt.Println("Running React App")
    fmt.Println("Current directories/files being watched:")
    for _, directory := range directoriesBeingWatch {
        fmt.Println("\t",directory)
    }
    fmt.Println("")
}

func main() {
    getDevServerConfig()

    var (
        serverPath string = settings.ServerPath
        directoriesToWatch []string = settings.DirectoriesToWatch
        port string = settings.Port
        host string = settings.Host
    )

    startApp(serverPath, port, host)
    initConsole(directoriesToWatch)

    watcher, err := fsnotify.NewWatcher()
    if err != nil {
        log.Fatal(err)
    }
    defer watcher.Close()

    done := make(chan bool)

    go func() {
        for {
        select {
            case event := <-watcher.Events:
                if event.Op&fsnotify.Write == fsnotify.Write {
                    restartApp(serverPath, port, host)
                }
            case err := <-watcher.Errors:
                log.Println("error:", err)
            }
        }
    }()

    for _, directory := range directoriesToWatch {
        err = watcher.Add(directory)
        if err != nil {
            log.Fatal(err)
        }
    }

    <-done
}
