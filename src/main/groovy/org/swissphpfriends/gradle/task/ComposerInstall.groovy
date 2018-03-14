package org.swissphpfriends.gradle.task

import org.gradle.api.tasks.TaskAction
import org.swissphpfriends.gradle.AbstractBaseTask

class ComposerInstall extends AbstractBaseTask {

    Boolean doNotUpdatePhar = false

    @TaskAction
    def run() {

        this.checkPhpInstallation();

        if (!this.isAvailable()) {
            this.installPhar();

        } else if (this.doNotUpdatePhar.equals(false)) {
            this.checkForSelfUpdate();
        }

        // install composer dependencies
        this.printBuildMessage('installing dependencies using composer...');

        Process composerInstallProcess = new ProcessBuilder("php", "composer.phar", "install")
                .directory(new File(this.workingDirectory))
                .redirectErrorStream(true)
                .start()
        composerInstallProcess.inputStream.eachLine { println it }
        composerInstallProcess.waitFor()
    }

    /**
     * @return boolean True if composer.phar is available and working
     */
    private boolean isAvailable() {
        def composerVersionProcess = new ProcessBuilder('php', 'composer.phar', '--version')
                .directory(new File(this.workingDirectory))
                .redirectErrorStream(true)
                .start()

        composerVersionProcess.waitFor();

        if (composerVersionProcess.exitValue()) {
            return false;
        }

        return true;
    }

    private void installPhar() {
        this.printBuildMessage('composer.phar not presend in current directory. Installing...');

        // download installer
        def address = 'https://getcomposer.org/installer'
        def path = this.workingDirectory + '/' + address.tokenize("/")[-1]
        def file = new FileOutputStream(path)
        def out = new BufferedOutputStream(file)
        out << new URL(address).openStream()
        out.close()

        // install composer.phar
        def installComposerProcess = new ProcessBuilder('php', './installer')
                .directory(new File(this.workingDirectory))
                .redirectErrorStream(true)
                .start()

        installComposerProcess.inputStream.eachLine { println it }
        installComposerProcess.waitFor();

        if (installComposerProcess.exitValue()) {
            println "Composer installation failed!"
        }

        File installer = new File(path);
        installer.delete();
    }

    private void checkForSelfUpdate() {
        this.printBuildMessage('composer.phar already present. Checking for update...');

        Process composerSelfupdateProcess = new ProcessBuilder("php", "composer.phar", "selfupdate")
                .directory(new File(this.workingDirectory))
                .redirectErrorStream(true)
                .start()
        composerSelfupdateProcess.inputStream.eachLine { println it }
        composerSelfupdateProcess.waitFor()

        if (composerSelfupdateProcess.exitValue()) {
            println "Composer installation failed!"
        }
    }
}